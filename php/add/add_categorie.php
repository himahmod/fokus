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
$titre       = valider_texte_obligatoire($_POST['nom']         ?? null, 20);
$couleur     = valider_couleur($_POST['color']                 ?? null);
$description = valider_texte_facultatif($_POST['description']  ?? null, 200);

/* 3. Insertion en BDD */
try {
    $connexion = getConnexion();
    $sql = "INSERT INTO CATEGORIE (UTILISATEUR_C, NOM_C, DESC_C, COLOR_C) 
            VALUES (:user, :titre, :description, :couleur)";
    $stmt = $connexion->prepare($sql);
    $stmt->execute([
        ':user'        => $user,
        ':titre'       => $titre,
        ':description' => $description,
        ':couleur'     => $couleur,
    ]);
    $connexion = null;
} catch (PDOException $e) {
    gerer_erreur_pdo($e, 'add_categorie');
}

/* 4. Succès */
rediriger_succes('cat_added');
?>