<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../BDD/bdd.php';
require_once __DIR__ . '/../security.php';

/* 1. Méthode HTTP, authentification, CSRF */
exiger_methode_post();
$user = exiger_utilisateur_connecte();
verifier_csrf();

/* 2. Mot de passe actuel obligatoire (pour confirmer l'identité) */
$curr_password = (string)($_POST['curr_password'] ?? '');
if ($curr_password === '') {
    rediriger_erreur('mdp_oblg');
}

/* 3. On regarde ce que l'utilisateur veut modifier
   Il peut modifier le mot de passe ET/OU la question secrète. */
$new_password     = (string)($_POST['new_password']     ?? '');
$confirm_password = (string)($_POST['confirm_password'] ?? '');
$question         = (string)($_POST['question']         ?? '');
$reponse          = (string)($_POST['reponse']          ?? '');

$veut_changer_mdp      = ($new_password !== '' || $confirm_password !== '');
$veut_changer_question = ($question !== '' || $reponse !== '');

// Au moins une des deux actions doit être demandée
if (!$veut_changer_mdp && !$veut_changer_question) {
    rediriger_erreur('champs_vides');
}

/* 4. Vérification du mot de passe actuel */
exiger_mot_de_passe_actuel($user, $curr_password);

/* 5. Validation conditionnelle des nouveaux champs */
if ($veut_changer_mdp) {
    // Les deux champs doivent être remplis
    if ($new_password === '' || $confirm_password === '') {
        rediriger_erreur('champs_vides');
    }
    // Robustesse du nouveau mdp
    $new_password = valider_mot_de_passe($new_password);
    // Confirmation identique
    exiger_mots_de_passe_identiques($new_password, $confirm_password);
}

if ($veut_changer_question) {
    // Les deux champs doivent être remplis ensemble
    if ($question === '' || $reponse === '') {
        rediriger_erreur('champs_vides');
    }
    // QUESTION est un INT en BDD → valider_id
    $question = valider_id($question);
    $reponse  = valider_texte_obligatoire($reponse, 30);
}

/* 6. Mise à jour en BDD */
try {
    $connexion = getConnexion();

    // On construit l'UPDATE dynamiquement selon ce qui change
    $set    = [];
    $params = [':user' => $user];

    if ($veut_changer_mdp) {
        $set[]              = 'PASSEWORD = :pass';
        $params[':pass']    = hasher_mot_de_passe($new_password);
    }
    if ($veut_changer_question) {
        $set[]            = 'QUESTION = :qst';
        $set[]            = 'REPONSE  = :rep';
        $params[':qst']   = $question;
        $params[':rep']   = $reponse;
    }

    $sql  = "UPDATE PERSONNE SET " . implode(', ', $set) . " WHERE NOM_P = :user";
    $stmt = $connexion->prepare($sql);
    $stmt->execute($params);

    $connexion = null;
} catch (PDOException $e) {
    gerer_erreur_pdo($e, 'reset_password');
}

if ($veut_changer_mdp) {
    $_SESSION['notif_password'] = date('H:i');
}

/* 7. Succès */
rediriger_succes('mdp_modifie');
?>