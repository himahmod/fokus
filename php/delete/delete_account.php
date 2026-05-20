<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../BDD/bdd.php';
require_once __DIR__ . '/../security.php';

/* 1. Méthode HTTP, authentification, CSRF */
$user = exiger_utilisateur_connecte();


/* 3. Suppression en cascade (ordre important à cause des FK) */
try {
    $connexion = getConnexion();

    $connexion->prepare("DELETE FROM TACHES    WHERE UTILISATEUR_T = :user")->execute([':user' => $user]);
    $connexion->prepare("DELETE FROM CATEGORIE WHERE UTILISATEUR_C = :user")->execute([':user' => $user]);
    $connexion->prepare("DELETE FROM OBJECTIF  WHERE UTILISATEUR_O = :user")->execute([':user' => $user]);
    $connexion->prepare("DELETE FROM PERSONNE  WHERE NOM_P         = :user")->execute([':user' => $user]);

    $connexion = null;
} catch (PDOException $e) {
    gerer_erreur_pdo($e, 'delete_account');
}

/* 4. Destruction de la session */
session_unset();
session_destroy();

/* 5. Redirection vers la page de garde (URL fixe, hors session) */
header("Location: /html/page_de_garde.php?success=compte_supprime");
exit;
?>