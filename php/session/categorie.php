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

    // Derive per-category data from the already-fetched $toutes_taches (no N+1 queries)
    foreach ($categories as &$cat) {
        $cat_id = $cat['ID_C'];
        $cat['taches']    = array_values(array_filter($toutes_taches, fn($t) => (int)$t['CATEGORIE_T'] === (int)$cat_id));
        $cat['nb_taches'] = count($cat['taches']);
    }
    unset($cat);

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