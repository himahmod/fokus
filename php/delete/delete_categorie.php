<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../BDD/bdd.php';
require_once __DIR__ . '/../security.php';

/* 1. Authentification (pas de méthode POST ni CSRF car appel GET) */
$user = exiger_utilisateur_connecte();

/* 2. Validation de l'id */
$id = valider_id($_GET['id'] ?? null);

/* 3. Suppression dans une transaction */
try {
    $connexion = getConnexion();
    $connexion->beginTransaction();

    // a) Détacher les tâches de cette catégorie
    $stmt = $connexion->prepare("UPDATE TACHES 
                                 SET CATEGORIE_T = NULL 
                                 WHERE CATEGORIE_T = :id AND UTILISATEUR_T = :user");
    $stmt->execute([':id' => $id, ':user' => $user]);

    // b) Supprimer la catégorie (en vérifiant qu'elle appartient à l'utilisateur)
    $stmt = $connexion->prepare("DELETE FROM CATEGORIE 
                                 WHERE ID_C = :id AND UTILISATEUR_C = :user");
    $stmt->execute([':id' => $id, ':user' => $user]);

    $connexion->commit();
    $connexion = null;
} catch (PDOException $e) {
    if (isset($connexion) && $connexion->inTransaction()) {
        $connexion->rollBack();
    }
    gerer_erreur_pdo($e, 'delete_categorie');
}

/* 4. Succès */
rediriger_succes('cat_deleted');
?>