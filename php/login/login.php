<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../BDD/bdd.php';
require_once __DIR__ . '/../security.php';

/* 1. Méthode HTTP */
exiger_methode_post();

/* 2. Validation des champs */
$email    = valider_email($_POST['email'] ?? null);
$password = (string)($_POST['password'] ?? '');
if ($password === '') {
    header("Location: ../../html/page_login.php?error=champs_vides");
    exit;
}

/* 3. Recherche de l'utilisateur + vérification mot de passe */
try {
    $connexion = getConnexion();
    $stmt = $connexion->prepare("SELECT NOM_P, PASSEWORD FROM PERSONNE WHERE MAIL = :mail");
    $stmt->execute([':mail' => $email]);
    $user = $stmt->fetch();

    if (!$user || !verifier_mot_de_passe($password, $user['PASSEWORD'])) {
        // Même message pour "email inconnu" et "mauvais mdp"
        // → empêche l'énumération de comptes
        header("Location: ../../html/page_login.php?error=identifiants_incorrects");
        exit;
    }

    /* 4. Mise à jour de la dernière connexion */
    $stmt = $connexion->prepare("UPDATE PERSONNE SET LAST_CONNEXION = :last_cnx WHERE NOM_P = :nom");
    $stmt->execute([
        ':last_cnx' => date('Y-m-d'),
        ':nom'      => $user['NOM_P'],
    ]);

    $connexion = null;
} catch (PDOException $e) {
    gerer_erreur_pdo($e, 'login');
}

/* 5. Session sécurisée : on régénère l'ID après login
   pour prévenir les attaques de session fixation */
session_regenerate_id(true);
$_SESSION['user'] = $user['NOM_P'];

/* 6. On génère le token CSRF pour les actions à venir */
generer_csrf_token();

/* 7. Redirection vers le dashboard */
header("Location: ../../html/dashboard.php");
exit;
?>