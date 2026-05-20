<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../BDD/bdd.php';
require_once __DIR__ . '/../security.php';

/* 1. Méthode HTTP, authentification, CSRF */
exiger_methode_post();
$user = exiger_utilisateur_connecte();
verifier_csrf();

/* 2. Validation de l'id */
$id_tache = valider_id($_POST['task_id'] ?? null);

/* 3. Récupération de l'ancienne tâche (pour fallback)
   Si elle n'existe pas ou n'appartient pas à l'user → erreur. */
try {
    $connexion = getConnexion();
    $stmt = $connexion->prepare("SELECT * FROM TACHES 
                                 WHERE ID_T = :id AND UTILISATEUR_T = :user");
    $stmt->execute([':id' => $id_tache, ':user' => $user]);
    $ancienne_tache = $stmt->fetch();

    if (!$ancienne_tache) {
        rediriger_erreur('champs_vides');
    }

    /* 4. Récupération des nouvelles valeurs (avec fallback sur anciennes) */
    $titre_brut       = $_POST['titre']        ?? '';
    $statut_brut      = $_POST['statut']       ?? '';
    $description_brut = $_POST['description']  ?? null;
    $date_brut        = $_POST['date_limite']  ?? null;
    $categorie_brut   = $_POST['categorie_id'] ?? null;

    // Titre : si vide on garde l'ancien, sinon on valide
    $titre = ($titre_brut !== '')
        ? valider_texte_obligatoire($titre_brut, 100)
        : $ancienne_tache['NOM_T'];

    // Statut : pareil, fallback sur l'ancien si vide
    $statut = ($statut_brut !== '')
        ? valider_dans_liste($statut_brut, ['en_cours', 'a_faire', 'termine'])
        : $ancienne_tache['STATUT'];

    // Description : facultative, fallback si absente du POST
    $description = array_key_exists('description', $_POST)
        ? valider_texte_facultatif($description_brut, 200)
        : $ancienne_tache['DESC_T'];

    // Date limite : facultative, fallback si absente du POST
    $date_limite = array_key_exists('date_limite', $_POST)
        ? valider_date_facultative($date_brut)
        : $ancienne_tache['LIMIT_DATE'];

    // Catégorie : facultative, fallback si absente du POST
    if (array_key_exists('categorie_id', $_POST)) {
        $categorie_id = !empty($categorie_brut) ? valider_id($categorie_brut) : null;
    } else {
        $categorie_id = $ancienne_tache['CATEGORIE_T'];
    }

    /* 5. Mise à jour */
    $sql = "UPDATE TACHES SET 
                NOM_T       = :titre,
                CATEGORIE_T = :categorie,
                DESC_T      = :description,
                STATUT      = :statut,
                LIMIT_DATE  = :date_limite
            WHERE ID_T = :id AND UTILISATEUR_T = :user";
    $stmt = $connexion->prepare($sql);
    $stmt->execute([
        ':titre'       => $titre,
        ':categorie'   => $categorie_id,
        ':description' => $description,
        ':statut'      => $statut,
        ':date_limite' => $date_limite,
        ':id'          => $id_tache,
        ':user'        => $user,
    ]);

    $connexion = null;
} catch (PDOException $e) {
    gerer_erreur_pdo($e, 'edit_task');
}

/* 6. Succès */
rediriger_succes('task_edited');
?>