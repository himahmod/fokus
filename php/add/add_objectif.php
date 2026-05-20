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
$titre = valider_texte_obligatoire($_POST['objectif'] ?? null, 200);

/* 3. Insertion en BDD */
try {
    $connexion = getConnexion();
    $sql = "INSERT INTO OBJECTIF (UTILISATEUR_O, TITRE_O) 
            VALUES (:user, :titre)";
    $stmt = $connexion->prepare($sql);
    $stmt->execute([
        ':user'  => $user,
        ':titre' => $titre,
    ]);
    $connexion = null;
} catch (PDOException $e) {
    gerer_erreur_pdo($e, 'add_objectif');
}

/* 4. Succès */
rediriger_succes('goal_added');
?>