<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../BDD/bdd.php';
require_once __DIR__ . '/../security.php';

/* 1. Authentification */
$user = exiger_utilisateur_connecte();

/* 2. Validation de l'id */
$id = valider_id($_GET['id'] ?? null);

/* 3. Suppression */
try {
    $connexion = getConnexion();
    $stmt = $connexion->prepare("DELETE FROM TACHES 
                                 WHERE ID_T = :id AND UTILISATEUR_T = :user");
    $stmt->execute([':id' => $id, ':user' => $user]);
    $connexion = null;
} catch (PDOException $e) {
    gerer_erreur_pdo($e, 'delete_task');
}

/* 4. Succès */
rediriger_succes('task_deleted');
?>