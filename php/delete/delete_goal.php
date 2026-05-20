<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../BDD/bdd.php';
require_once __DIR__ . '/../security.php';

/* 1. Authentification */
$user = exiger_utilisateur_connecte();

/* 2. Validation du titre */
$titre = valider_texte_obligatoire($_GET['titre'] ?? null, 200);

/* 3. Suppression */
try {
    $connexion = getConnexion();
    $stmt = $connexion->prepare("DELETE FROM OBJECTIF 
                                 WHERE TITRE_O = :titre AND UTILISATEUR_O = :user");
    $stmt->execute([':titre' => $titre, ':user' => $user]);
    $connexion = null;
} catch (PDOException $e) {
    gerer_erreur_pdo($e, 'delete_goal');
}

/* 4. Succès */
rediriger_succes('goal_deleted');
?>