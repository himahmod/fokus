<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../BDD/bdd.php';
require_once __DIR__ . '/../security.php';

$user = exiger_utilisateur_connecte();
exiger_methode_post();
verifier_csrf();

try {
    $connexion = getConnexion();
    $stmt = $connexion->prepare("DELETE FROM PHOTO_PROFIL WHERE UTILISATEUR = :user");
    $stmt->execute([':user' => $user]);
    $connexion = null;
} catch (PDOException $e) {
    gerer_erreur_pdo($e, 'delete_photo');
}

unset($_SESSION['photo_profil']);
rediriger_succes('photo_deleted');
?>