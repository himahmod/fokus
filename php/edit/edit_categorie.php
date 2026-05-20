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
$cat_id      = valider_id($_POST['cat_id']                    ?? null);
$nom         = valider_texte_obligatoire($_POST['nom']        ?? null, 20);
$couleur     = valider_couleur($_POST['color']                ?? null);
$description = valider_texte_facultatif($_POST['description'] ?? null, 200);

/* 3. Mise à jour
   On filtre sur ID_C ET UTILISATEUR_C → si la catégorie
   n'appartient pas à l'utilisateur, l'UPDATE ne touche rien.
   Pas besoin de SELECT préalable. */
try {
    $connexion = getConnexion();
    $sql = "UPDATE CATEGORIE SET 
                NOM_C   = :nom,
                DESC_C  = :description,
                COLOR_C = :couleur
            WHERE ID_C = :id AND UTILISATEUR_C = :user";
    $stmt = $connexion->prepare($sql);
    $stmt->execute([
        ':nom'         => $nom,
        ':description' => $description,
        ':couleur'     => $couleur,
        ':id'          => $cat_id,
        ':user'        => $user,
    ]);
    $connexion = null;
} catch (PDOException $e) {
    gerer_erreur_pdo($e, 'edit_categorie');
}

/* 4. Succès */
rediriger_succes('cat_edited');
?>