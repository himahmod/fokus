<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../BDD/bdd.php';
require_once __DIR__ . '/../security.php';

/* 1. Méthode HTTP, authentification, CSRF */
$user = exiger_utilisateur_connecte();

/* 2. Suppression */
try {
    $connexion = getConnexion();

    // D'abord détacher les tâches de leurs catégories
    $stmt = $connexion->prepare("UPDATE TACHES SET CATEGORIE_T = NULL 
                                 WHERE UTILISATEUR_T = :user");
    $stmt->execute([':user' => $user]);

    // Ensuite supprimer toutes les catégories de l'utilisateur
    $stmt = $connexion->prepare("DELETE FROM CATEGORIE WHERE UTILISATEUR_C = :user");
    $stmt->execute([':user' => $user]);

    $connexion = null;
} catch (PDOException $e) {
    gerer_erreur_pdo($e, 'delete_allcategories');
}

/* 3. Succès */
rediriger_succes('cats_deleted');
?>