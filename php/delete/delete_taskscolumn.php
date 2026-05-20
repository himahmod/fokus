<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../BDD/bdd.php';
require_once __DIR__ . '/../security.php';

/* 1. Authentification */
$user = exiger_utilisateur_connecte();

/* 2. Validation du statut (liste fermée) */
$statut = valider_dans_liste($_GET['statut'] ?? null, ['en_cours', 'a_faire', 'termine']);

/* 3. Suppression */
try {
    $connexion = getConnexion();
    $stmt = $connexion->prepare("DELETE FROM TACHES 
                                 WHERE STATUT = :statut AND UTILISATEUR_T = :user");
    $stmt->execute([':statut' => $statut, ':user' => $user]);
    $connexion = null;
} catch (PDOException $e) {
    gerer_erreur_pdo($e, 'delete_taskscolumn');
}

$_SESSION['notif_delete'] = date('H:i');

/* 4. Succès */
rediriger_succes('tasks_deleted_col');
?>