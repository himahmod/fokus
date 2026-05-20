<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../BDD/bdd.php';
require_once __DIR__ . '/../security.php';

/* 1. Méthode HTTP, authentification, CSRF */
exiger_methode_post();
$user = exiger_utilisateur_connecte();
verifier_csrf();

/* 2. Mot de passe actuel obligatoire (modif sensible : nom, email…) */
$password = (string)($_POST['curr_password'] ?? '');
if ($password === '') {
    rediriger_erreur('mdp_oblg');
}
exiger_mot_de_passe_actuel($user, $password);

/* 3. Récupération des infos actuelles pour fallback */
$connexion = getConnexion();
try { 
    $stmt = $connexion->prepare("SELECT NOM_P, MAIL, NB_TACHES FROM PERSONNE WHERE NOM_P = :user");
    $stmt->execute([':user' => $user]);
    $data_user = $stmt->fetch();
} catch (PDOException $e) {
    gerer_erreur_pdo($e, 'update_profil_select');
}

/* 4. Récupération + validation des nouvelles valeurs (avec fallback) */
$nouveau_nom_brut      = trim((string)($_POST['username'] ?? ''));
$nouveau_email_brut    = trim((string)($_POST['email']    ?? ''));
$nouveau_nb_tache_brut = trim((string)($_POST['nb_tache'] ?? ''));

// Nom : si vide, on garde l'ancien ; sinon validation (max 20 = VARCHAR(20))
$nouveau_nom = ($nouveau_nom_brut === '')
    ? $user
    : valider_texte_obligatoire($nouveau_nom_brut, 20);

// Email : si vide, on garde l'ancien ; sinon validation
$nouveau_email = ($nouveau_email_brut === '')
    ? $data_user['MAIL']
    : valider_email($nouveau_email_brut);

// NB_TACHES : si vide, on garde l'ancien ; sinon doit être un entier 1-100
if ($nouveau_nb_tache_brut === '') {
    $nouveau_nb_tache = $data_user['NB_TACHES'];
} else {
    $n = valider_id($nouveau_nb_tache_brut);
    if ($n > 100) {
        rediriger_erreur('nb_taches_invalide');
    }
    $nouveau_nb_tache = $n;
}

/* 5. Mise à jour
   Les ALTER TABLE pour ajouter ON UPDATE CASCADE ont été
   retirés d'ici — ils doivent être faits UNE SEULE FOIS dans
   le script SQL de création de la base, pas à chaque update. */
try {
    $sql = "UPDATE PERSONNE SET 
                NOM_P     = :new_nom,
                MAIL      = :mail,
                NB_TACHES = :nb_taches
            WHERE NOM_P = :nom";
    $stmt = $connexion->prepare($sql);
    $stmt->execute([
        ':new_nom'   => $nouveau_nom,
        ':mail'      => $nouveau_email,
        ':nb_taches' => $nouveau_nb_tache,
        ':nom'       => $user,
    ]);

    $connexion = null;
} catch (PDOException $e) {
    gerer_erreur_pdo($e, 'update_profil_update');
}

/* 6. Mise à jour de la session avec le nouveau nom */
$_SESSION['user'] = $nouveau_nom;

/* 7. Succès */
rediriger_succes('profil_updated');
?>