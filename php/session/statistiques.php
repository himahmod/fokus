<?php
require_once __DIR__ . '/../../BDD/bdd.php';
require_once __DIR__ . '/../../php/security.php';

/* ── Authentification ── */
$username = exiger_utilisateur_connecte();

$connexion = getConnexion();

try {
    /* ── Compteurs par statut (une seule requête) ── */
    $req = $connexion->prepare(
        "SELECT
            COUNT(*) as total,
            SUM(STATUT = 'termine')  as termine,
            SUM(STATUT = 'en_cours') as en_cours,
            SUM(STATUT = 'a_faire')  as a_faire,
            SUM(CATEGORIE_T IS NULL) as sans_cat,
            SUM(CATEGORIE_T IS NOT NULL) as avec_cat
         FROM TACHES WHERE UTILISATEUR_T = :nom"
    );
    $req->execute([':nom' => $username]);
    $stats = $req->fetch();

    $nb_taches          = (int)$stats['total'];
    $nb_taches_termines = (int)$stats['termine'];
    $nb_taches_en_cours = (int)$stats['en_cours'];
    $nb_taches_a_faire  = (int)$stats['a_faire'];
    $nb_sans_categories = (int)$stats['sans_cat'];
    $nb_avec_cat        = (int)$stats['avec_cat'];

    // Urgent (basé sur le champ URGENT, pas le statut)
    $req = $connexion->prepare(
        "SELECT COUNT(*) FROM TACHES WHERE UTILISATEUR_T = :nom AND URGENT = 1"
    );
    $req->execute([':nom' => $username]);
    $nb_taches_urgent = (int)$req->fetchColumn();

    /* ── Catégories utilisées ── */
    $req = $connexion->prepare(
        "SELECT COUNT(DISTINCT CATEGORIE_T) FROM TACHES
         WHERE UTILISATEUR_T = :nom AND CATEGORIE_T IS NOT NULL"
    );
    $req->execute([':nom' => $username]);
    $nb_cat_utilisees = (int)$req->fetchColumn();

    $nb_tache_avg = $nb_cat_utilisees > 0 ? round($nb_avec_cat / $nb_cat_utilisees, 1) : 0;

    /* ── Objectifs ── */
    $req = $connexion->prepare(
        "SELECT TITRE_O FROM OBJECTIF WHERE UTILISATEUR_O = :nom"
    );
    $req->execute([':nom' => $username]);
    $objectifs = $req->fetchAll();

    /* ── Répartition par catégorie ── */
    $req = $connexion->prepare(
        "SELECT c.NOM_C, c.COLOR_C, COUNT(t.ID_T) as nb
         FROM CATEGORIE c
         LEFT JOIN TACHES t ON t.CATEGORIE_T = c.ID_C AND t.UTILISATEUR_T = :nom
         WHERE c.UTILISATEUR_C = :nom
         GROUP BY c.ID_C, c.NOM_C, c.COLOR_C
         ORDER BY nb DESC"
    );
    $req->execute([':nom' => $username]);
    $repartition_categories = $req->fetchAll();

} catch (PDOException $e) {
    gerer_erreur_pdo($e, 'statistiques.php');
}

$connexion = null;

/* ── Progression globale ── */
$pct_progression = $nb_taches > 0 ? round(($nb_taches_termines / $nb_taches) * 100) : 0;
?>