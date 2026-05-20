<?php
require_once __DIR__ . '/data_user.php';
require_once __DIR__ . '/../../php/security.php';

// Session déjà vérifiée dans data_user.php — on récupère juste le user
$user = $username; // défini par data_user.php

$connexion = getConnexion();

try {
    // Catégories de l'utilisateur
    $req = $connexion->prepare(
        "SELECT ID_C, NOM_C, DESC_C, COLOR_C FROM CATEGORIE WHERE UTILISATEUR_C = :nom"
    );
    $req->execute([':nom' => $user]);
    $categories = $req->fetchAll();

    // Pour chaque catégorie : nb de tâches + liste des tâches
    foreach ($categories as &$cat) {
        $req2 = $connexion->prepare(
            "SELECT COUNT(*) FROM TACHES WHERE CATEGORIE_T = :id AND UTILISATEUR_T = :nom"
        );
        $req2->execute([':id' => $cat['ID_C'], ':nom' => $user]);
        $cat['nb_taches'] = $req2->fetchColumn();

        $req3 = $connexion->prepare(
            "SELECT ID_T, NOM_T, STATUT, LIMIT_DATE FROM TACHES
             WHERE CATEGORIE_T = :id AND UTILISATEUR_T = :nom
             ORDER BY LIMIT_DATE ASC"
        );
        $req3->execute([':id' => $cat['ID_C'], ':nom' => $user]);
        $cat['taches'] = $req3->fetchAll();
    }
    unset($cat);

    // Toutes les tâches avec catégorie
    $req = $connexion->prepare(
        "SELECT t.*, c.NOM_C as NOM_CAT, c.COLOR_C as COLOR_CAT
         FROM TACHES t
         LEFT JOIN CATEGORIE c ON t.CATEGORIE_T = c.ID_C
         WHERE t.UTILISATEUR_T = :nom
         ORDER BY t.LIMIT_DATE ASC"
    );
    $req->execute([':nom' => $user]);
    $toutes_taches = $req->fetchAll();

} catch (PDOException $e) {
    gerer_erreur_pdo($e, 'categorie.php');
}

$connexion = null;

// Filtres par statut
$taches_en_cours = array_values(array_filter($toutes_taches, fn($t) => trim($t['STATUT']) === 'en_cours'));
$taches_a_faire  = array_values(array_filter($toutes_taches, fn($t) => trim($t['STATUT']) === 'a_faire'));
$taches_termines = array_values(array_filter($toutes_taches, fn($t) => trim($t['STATUT']) === 'termine'));
$taches_urgent   = array_values(array_filter($toutes_taches, fn($t) => !empty($t['URGENT'])));

// Notifications
$today = date('Y-m-d');

$taches_en_retard = array_values(array_filter($toutes_taches, fn($t) =>
    $t['LIMIT_DATE'] && $t['LIMIT_DATE'] < $today && $t['STATUT'] !== 'termine'
));
$taches_urgentes_actives = array_values(array_filter($toutes_taches, fn($t) =>
    !empty($t['URGENT']) && $t['STATUT'] !== 'termine'
));
$taches_aujourdhui = array_values(array_filter($toutes_taches, fn($t) =>
    $t['LIMIT_DATE'] === $today && $t['STATUT'] !== 'termine'
));
?>