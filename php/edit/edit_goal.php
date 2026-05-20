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
$ancien_titre  = valider_texte_obligatoire($_POST['ancien_titre']  ?? null, 200);
$nouveau_titre = valider_texte_obligatoire($_POST['nouveau_titre'] ?? null, 200);

/* 3. Mise à jour
   Le WHERE inclut UTILISATEUR_O → si l'objectif n'appartient
   pas à l'utilisateur, rien n'est modifié (silencieusement). */
try {
    $connexion = getConnexion();
    $sql = "UPDATE OBJECTIF SET TITRE_O = :nouveau_titre 
            WHERE TITRE_O = :ancien_titre AND UTILISATEUR_O = :user";
    $stmt = $connexion->prepare($sql);
    $stmt->execute([
        ':nouveau_titre' => $nouveau_titre,
        ':ancien_titre'  => $ancien_titre,
        ':user'          => $user,
    ]);
    $connexion = null;
} catch (PDOException $e) {
    gerer_erreur_pdo($e, 'edit_goal');
}

/* 4. Succès */
rediriger_succes('goal_edited');
?>