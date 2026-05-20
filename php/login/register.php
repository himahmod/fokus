<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../BDD/bdd.php';
require_once __DIR__ . '/../security.php';

/* 1. Méthode HTTP */
exiger_methode_post();

/* 2. Validation des champs */
$username         = valider_texte_obligatoire($_POST['username']          ?? null, 20);
$email            = valider_email($_POST['email']                          ?? null);
$password         = valider_mot_de_passe($_POST['password']                ?? null);
$confirm          = (string)($_POST['confirm_password']                    ?? '');
$question_secrete = valider_id($_POST['question_secrete']                  ?? null);
$reponse          = valider_texte_obligatoire($_POST['reponse']            ?? null, 30);
$terms = isset($_POST['terms']) ? 1 : 0;

if($terms == 0){
    rediriger_erreur('cgu', '/html/page_login.php');
}
// Username : longueur minimale 3 (max déjà vérifié par valider_texte_obligatoire)
if (mb_strlen($username) < 3) {
    rediriger_erreur('username_invalide', '/html/page_login.php');
}

// Confirmation du mot de passe
exiger_mots_de_passe_identiques($password, $confirm);


// Question secrète : doit être dans la liste valide
if (!in_array($question_secrete, [1, 2, 3, 4], true)) {
    rediriger_erreur('question_invalide', '/html/page_login.php');
}

/* 3. Vérifications d'unicité + insertion */
try {
    $connexion = getConnexion();

    // Username déjà pris ?
    $stmt = $connexion->prepare("SELECT 1 FROM PERSONNE WHERE NOM_P = :nom");
    $stmt->execute([':nom' => $username]);
    if ($stmt->fetchColumn()) {
        rediriger_erreur('username_pris', '/html/page_login.php');
    }

    // Email déjà pris ?
    $stmt = $connexion->prepare("SELECT 1 FROM PERSONNE WHERE MAIL = :mail");
    $stmt->execute([':mail' => $email]);
    if ($stmt->fetchColumn()) {
        rediriger_erreur('email_pris', '/html/page_login.php');
    }

    // Insertion
    $sql = "INSERT INTO PERSONNE (NOM_P, PASSEWORD, MAIL, LAST_CONNEXION, SIGNIN_DATE, ROLE_P, QUESTION, REPONSE) 
            VALUES (:nom, :mdp, :mail, :last_cnx, :signin_date, :role_p, :question, :reponse)";
    $stmt = $connexion->prepare($sql);
    $stmt->execute([
        ':nom'         => $username,
        ':mdp'         => hasher_mot_de_passe($password),
        ':mail'        => $email,
        ':last_cnx'    => date('Y-m-d'),
        ':signin_date' => date('Y-m-d'),
        ':role_p'      => 'member',
        ':question'    => $question_secrete,
        ':reponse'     => $reponse,
    ]);

    $connexion = null;
} catch (PDOException $e) {
    gerer_erreur_pdo($e, 'register');
}

/* 4. Connexion automatique après inscription
   On régénère l'ID de session (anti session fixation) */
session_regenerate_id(true);
$_SESSION['user'] = $username;
generer_csrf_token();

/* 5. Redirection vers le dashboard */
header("Location: ../../html/dashboard.php");
exit;
?>