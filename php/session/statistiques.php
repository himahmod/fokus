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
            COUNT(*) as TOTAL,
            COUNT(*) FILTER (WHERE STATUT = 'termine')       as TERMINE,
            COUNT(*) FILTER (WHERE STATUT = 'en_cours')      as EN_COURS,
            COUNT(*) FILTER (WHERE STATUT = 'a_faire')       as A_FAIRE,
            COUNT(*) FILTER (WHERE CATEGORIE_T IS NULL)      as SANS_CAT,
            COUNT(*) FILTER (WHERE CATEGORIE_T IS NOT NULL)  as AVEC_CAT
         FROM TACHES WHERE UTILISATEUR_T = :nom"
    );
    $req->execute([':nom' => $username]);
    $stats = $req->fetch();

    $nb_taches          = (int)$stats['TOTAL'];
    $nb_taches_termines = (int)$stats['TERMINE'];
    $nb_taches_en_cours = (int)$stats['EN_COURS'];
    $nb_taches_a_faire  = (int)$stats['A_FAIRE'];
    $nb_sans_categories = (int)$stats['SANS_CAT'];
    $nb_avec_cat        = (int)$stats['AVEC_CAT'];

    // Urgent (basé sur le champ URGENT, pas le statut)
    $req = $connexion->prepare(
        "SELECT COUNT(*) FROM TACHES WHERE UTILISATEUR_T = :nom AND URGENT = TRUE"
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
        "SELECT c.NOM_C, c.COLOR_C, COUNT(t.ID_T) as NB
         FROM CATEGORIE c
         LEFT JOIN TACHES t ON t.CATEGORIE_T = c.ID_C AND t.UTILISATEUR_T = :nom
         WHERE c.UTILISATEUR_C = :nom
         GROUP BY c.ID_C, c.NOM_C, c.COLOR_C
         ORDER BY NB DESC"
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