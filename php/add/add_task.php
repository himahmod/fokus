<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../BDD/bdd.php';
require_once __DIR__ . '/../security.php';

/* 1. Méthode HTTP, authentification, CSRF */
exiger_methode_post();
$user = exiger_utilisateur_connecte();
verifier_csrf();

/* 2. Validation des champs */
$titre        = valider_texte_obligatoire($_POST['titre']        ?? null, 100);
$statut       = valider_dans_liste($_POST['statut']              ?? null, ['en_cours', 'a_faire', 'termine']);
$description  = valider_texte_facultatif($_POST['description']   ?? null, 200);
$date_limite  = valider_date_facultative($_POST['date_limite']   ?? null);

// Catégorie : facultative ; si présente, doit être un id valide
$categorie_id = null;
if (!empty($_POST['categorie_id'])) {
    $categorie_id = valider_id($_POST['categorie_id']);
}

/* 3. Insertion en BDD */
try {
    $connexion = getConnexion();
    $sql = "INSERT INTO TACHES (UTILISATEUR_T, NOM_T, CATEGORIE_T, DESC_T, STATUT, LIMIT_DATE) 
            VALUES (:user, :titre, :categorie, :description, :statut, :date_limite)";
    $stmt = $connexion->prepare($sql);
    $stmt->execute([
        ':user'        => $user,
        ':titre'       => $titre,
        ':categorie'   => $categorie_id,
        ':description' => $description,
        ':statut'      => $statut,
        ':date_limite' => $date_limite,
    ]);
    $connexion = null;
} catch (PDOException $e) {
    gerer_erreur_pdo($e, 'add_task');
}

/* 4. Succès */
rediriger_succes('task_added');
?>