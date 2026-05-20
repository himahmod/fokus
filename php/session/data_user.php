<?php
session_start();
require_once __DIR__ . '/../../BDD/bdd.php';
require_once __DIR__ . '/../../php/security.php';

/* ── Authentification ── */
$username = exiger_utilisateur_connecte();

$connexion = getConnexion();

try {
    /* ── Infos utilisateur ── */
    $req = $connexion->prepare("SELECT * FROM PERSONNE WHERE NOM_P = :nom");
    $req->execute([':nom' => $username]);
    $array = $req->fetch();

    $mail               = $array['MAIL']           ?? '';
    $signin_date        = $array['SIGNIN_DATE']    ?? '';
    $last_cnx           = $array['LAST_CONNEXION'] ?? '';
    $role_p             = $array['ROLE_P']         ?? '';
    $nb_taches_par_page = (int)($array['NB_TACHES'] ?? 5);
    $first_letter       = strtoupper($username[0]);

    /* ── Photo de profil ── */
    $req = $connexion->prepare("SELECT PHOTO FROM PHOTO_PROFIL WHERE UTILISATEUR = :nom");
    $req->execute([':nom' => $username]);
    $row = $req->fetch();
    $photo_profil = ($row && is_string($row['PHOTO'] ?? null)) ? $row['PHOTO'] : null;

    /* ── Toutes les tâches (compteurs dérivés après) ── */
    $req = $connexion->prepare(
        "SELECT t.*, c.NOM_C as NOM_CAT, c.COLOR_C as COLOR_CAT
         FROM TACHES t
         LEFT JOIN CATEGORIE c ON t.CATEGORIE_T = c.ID_C
         WHERE t.UTILISATEUR_T = :nom
         ORDER BY t.LIMIT_DATE ASC"
    );
    $req->execute([':nom' => $username]);
    $toutes_taches = $req->fetchAll();
    $nb_taches = count($toutes_taches);

    $req = $connexion->prepare("SELECT COUNT(*) FROM CATEGORIE WHERE UTILISATEUR_C = :nom");
    $req->execute([':nom' => $username]);
    $nb_categories = $req->fetchColumn();

    /* ── Données agenda ── */
    $req = $connexion->prepare(
        "SELECT NOM_T, STATUT, LIMIT_DATE FROM TACHES
         WHERE UTILISATEUR_T = :u
         AND LIMIT_DATE BETWEEN CURRENT_DATE AND CURRENT_DATE + INTERVAL '3 days'
         AND STATUT != 'termine'
         ORDER BY LIMIT_DATE ASC"
    );
    $req->execute([':u' => $username]);
    $taches_urgentes = $req->fetchAll();

    $req = $connexion->prepare(
        "SELECT T.ID_T, T.NOM_T, T.STATUT, T.LIMIT_DATE, C.NOM_C, C.COLOR_C
         FROM TACHES T LEFT JOIN CATEGORIE C ON T.CATEGORIE_T = C.ID_C
         WHERE T.UTILISATEUR_T = :u AND T.LIMIT_DATE IS NOT NULL
         ORDER BY T.LIMIT_DATE ASC"
    );
    $req->execute([':u' => $username]);
    $toutes_taches_agenda = $req->fetchAll();

} catch (PDOException $e) {
    gerer_erreur_pdo($e, 'data_user.php');
}

$connexion = null;

/* ── Filtres par statut ── */
$taches_en_cours = array_values(array_filter($toutes_taches, fn($t) => $t['STATUT'] === 'en_cours'));
$taches_a_faire  = array_values(array_filter($toutes_taches, fn($t) => $t['STATUT'] === 'a_faire'));
$taches_termines = array_values(array_filter($toutes_taches, fn($t) => $t['STATUT'] === 'termine'));
$taches_urgent   = array_values(array_filter($toutes_taches, fn($t) => !empty($t['URGENT'])));

/* ── Constantes globales ── */
$STATUT_COLORS = [
    'urgent'   => 'var(--status-urgent)',
    'en_cours' => 'var(--gold)',
    'a_faire'  => 'var(--status-todo)',
    'termine'  => 'var(--status-done)',
];
$STATUT_LABELS = [
    'urgent'   => 'Urgent',
    'en_cours' => 'En cours',
    'a_faire'  => 'À faire',
    'termine'  => 'Terminé',
];
$NOMS_MOIS = ['','Janvier','Février','Mars','Avril','Mai','Juin',
              'Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
$MOIS_FR = [
    'January'=>'Janvier','February'=>'Février','March'=>'Mars','April'=>'Avril',
    'May'=>'Mai','June'=>'Juin','July'=>'Juillet','August'=>'Août',
    'September'=>'Septembre','October'=>'Octobre','November'=>'Novembre','December'=>'Décembre'
];

/* ── Données calendrier ── */
$dates_taches    = array_values(array_filter(array_column($toutes_taches_agenda, 'LIMIT_DATE')));
$taches_par_date = [];
foreach ($toutes_taches_agenda as $t) {
    $taches_par_date[$t['LIMIT_DATE']][] = $t;
}

/* ── Notifications ── */
$today = date('Y-m-d');
$taches_en_retard        = array_values(array_filter($toutes_taches, fn($t) => $t['LIMIT_DATE'] && $t['LIMIT_DATE'] < $today && $t['STATUT'] !== 'termine'));
$taches_urgentes_actives = array_values(array_filter($toutes_taches, fn($t) => !empty($t['URGENT']) && $t['STATUT'] !== 'termine'));
$taches_aujourdhui       = array_values(array_filter($toutes_taches, fn($t) => $t['LIMIT_DATE'] === $today && $t['STATUT'] !== 'termine'));

$cal_mode = 'dashboard';
?>