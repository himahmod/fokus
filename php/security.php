<?php
declare(strict_types=1);

/* ==========================================================
   FOKUS — security.php
   Bibliothèque de fonctions de sécurité.
   ========================================================== */

/* ==========================================================
   1) REDIRECTION SÉCURISÉE
   - Bloque les open redirects (referer vers domaine externe)
   - Nettoie les anciens paramètres success/error
   - Ajoute le bon séparateur (? ou &)
   ========================================================== */
function rediriger(string $cle, bool $est_erreur = false, string $defaut = '/html/dashboard.php'): void
{
    $referer = $_SERVER['HTTP_REFERER'] ?? $defaut;

    // Bloque tout referer qui n'est pas sur notre host
    $host_actuel  = $_SERVER['HTTP_HOST'] ?? '';
    $host_referer = parse_url($referer, PHP_URL_HOST) ?? '';
    if ($host_referer !== '' && $host_referer !== $host_actuel) {
        $referer = $defaut;
    }

    // Nettoie un ancien paramètre success/error éventuel
    $referer = preg_replace('/[?&](success|error)=[^&]*/', '', $referer);
    $referer = rtrim($referer, '?&');

    $param = $est_erreur ? 'error' : 'success';
    $sep   = (strpos($referer, '?') === false) ? '?' : '&';
    header("Location: " . $referer . $sep . $param . "=" . urlencode($cle));
    exit;
}

/* Raccourcis pour plus de lisibilité */
function rediriger_succes(string $cle, string $defaut = '/html/dashboard.php'): void
{
    rediriger($cle, false, $defaut);
}

function rediriger_erreur(string $cle, string $defaut = '/html/dashboard.php'): void
{
    rediriger($cle, true, $defaut);
}


/* ==========================================================
   2) VÉRIFICATION MÉTHODE HTTP
   Bloque les requêtes GET sur les actions qui doivent être POST.
   ========================================================== */
function exiger_methode_post(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        rediriger_erreur('methode_invalide');
    }
}


/* ==========================================================
   3) AUTHENTIFICATION
   - Vérifie que l'utilisateur est connecté
   - Vérifie qu'il existe encore en BDD (compte non supprimé)
   Retourne le nom de l'utilisateur connecté.
   ========================================================== */
function exiger_utilisateur_connecte(): string
{
    $user = $_SESSION['user'] ?? '';

    if ($user === '') {
        header("Location: /html/page_login.php");
        exit;
    }

    // Vérifier que l'utilisateur existe vraiment en BDD
    require_once __DIR__ . '/../BDD/bdd.php';
    $connexion = getConnexion();
    $stmt = $connexion->prepare("SELECT 1 FROM PERSONNE WHERE NOM_P = :user");
    $stmt->execute([':user' => $user]);

    if (!$stmt->fetchColumn()) {
        session_unset();
        session_destroy();
        header("Location: /html/page_login.php");
        exit;
    }

    return $user;
}


/* ==========================================================
   4) PROTECTION CSRF
   - Génère un jeton si absent (à appeler à chaque session)
   - Vérifie le jeton envoyé dans les formulaires
   ========================================================== */
function generer_csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifier_csrf(): void
{
    $token_envoye  = $_POST['csrf_token'] ?? '';
    $token_session = $_SESSION['csrf_token'] ?? '';

    if ($token_session === '' || !hash_equals($token_session, $token_envoye)) {
        rediriger_erreur('csrf_invalide');
    }
}


/* ==========================================================
   5) VALIDATION DES CHAMPS
   Chaque fonction retourne la valeur nettoyée ou redirige
   avec une erreur si la validation échoue.
   ========================================================== */
/**
 * Texte obligatoire : non vide après trim, longueur max.
 * @param mixed $valeur     Valeur brute (string|null|tableau...)
 * @param int   $max        Longueur maximale (caractères)
 */
function valider_texte_obligatoire($valeur, int $max): string
{
    $v = trim((string)($valeur ?? ''));
    if ($v === '') {
        rediriger_erreur('champs_vides');
    }
    if (mb_strlen($v) > $max) {
        rediriger_erreur('champ_trop_long');
    }
    return $v;
}

/**
 * Texte facultatif : peut être vide. Retourne null si vide.
 * @param mixed $valeur     Valeur brute (string|null|tableau...)
 * @param int   $max        Longueur maximale (caractères)
 * @return string|null
 */
function valider_texte_facultatif($valeur, int $max): ?string
{
    $v = trim((string)($valeur ?? ''));
    if ($v === '') {
        return null;
    }
    if (mb_strlen($v) > $max) {
        rediriger_erreur('champ_trop_long');
    }
    return $v;
}

/**
 * Couleur hexadécimale : #RRGGBB ou #RGB.
 * @param mixed $valeur     Valeur brute (string|null|tableau...)
 */
function valider_couleur($valeur): string
{
    $v = trim((string)($valeur ?? ''));
    if (!preg_match('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/', $v)) {
        rediriger_erreur('couleur_invalide');
    }
    return $v;
}

/**
 * Identifiant entier : positif, dans une plage acceptable.
  * @param mixed $valeur     Valeur brute (string|null|tableau...)
 */
function valider_id($valeur): int
{
    if (!is_numeric($valeur)) {
        rediriger_erreur('champs_vides');
    }
    $id = (int)$valeur;
    if ($id < 1 || $id > PHP_INT_MAX) {
        rediriger_erreur('champs_vides');
    }
    return $id;
}

/**
 * Date au format YYYY-MM-DD.
 * Retourne null si vide, sinon la date validée.
 * @param mixed $valeur     Valeur brute (string|null|tableau...)
 */
function valider_date_facultative($valeur): ?string
{
    $v = trim((string)($valeur ?? ''));
    if ($v === '') {
        return null;
    }
    $d = DateTime::createFromFormat('Y-m-d', $v);
    if (!$d || $d->format('Y-m-d') !== $v) {
        rediriger_erreur('champs_vides');
    }
    return $v;
}

/**
 * Valeur dans une liste fermée (ex: statuts).
 * @param mixed $valeur     Valeur brute (string|null|tableau...)
 */
function valider_dans_liste($valeur, array $liste_valide): string
{
    $v = trim((string)($valeur ?? ''));
    if (!in_array($v, $liste_valide, true)) {
        rediriger_erreur('champs_vides');
    }
    return $v;
}

/**
 * Email valide.
 * @param mixed $valeur     Valeur brute (string|null|tableau...)
 */
function valider_email($valeur): string
{
    $v = trim((string)($valeur ?? ''));
    if (!filter_var($v, FILTER_VALIDATE_EMAIL) || mb_strlen($v) > 50) {
        rediriger_erreur('email_invalide');
    }
    return $v;
}

/* ==========================================================
   6) GESTION DU MOT DE PASSE
   ========================================================== */
/**
 * Valide un mot de passe en clair (au moment de l'inscription
 * ou du changement de mdp). Contrôle :
 *   - longueur minimale et maximale
 *   - au moins une minuscule, une majuscule, un chiffre
 *
 * Retourne le mot de passe en clair (validé) — à hasher ensuite
 * avec hasher_mot_de_passe() avant insertion en BDD.
 */
function valider_mot_de_passe($valeur, int $min = 8, int $max = 72): string
{
    $v = (string)($valeur ?? '');

    // Longueur minimale
    if (strlen($v) < $min) {
        rediriger_erreur('mdp_court');
    }


    if (strlen($v) > $max) {
        rediriger_erreur('mdp_trop_long');
    }

    if (!preg_match('/[a-z]/', $v)
     || !preg_match('/[A-Z]/', $v)
     || !preg_match('/[0-9]/', $v)) {
        rediriger_erreur('mdp_faible');
    }

    return $v;
}

/**
 * Vérifie que deux mots de passe (nouveau + confirmation) sont
 * identiques. 
 */
function exiger_mots_de_passe_identiques(string $mdp1, string $mdp2): void
{
    if (!hash_equals($mdp1, $mdp2)) {
        rediriger_erreur('mdp_different');
    }
}

/**
 * Hashe un mot de passe en clair avec l'algorithme par défaut
 * de PHP (actuellement bcrypt, mais évoluera). 
 */
function hasher_mot_de_passe(string $mdp_clair): string
{
    return password_hash($mdp_clair, PASSWORD_DEFAULT);
}

/**
 * Vérifie un mot de passe saisi contre le hash stocké en BDD.
 * Retourne true si correct, false sinon. Ne redirige pas
 */
function verifier_mot_de_passe(string $mdp_clair, string $hash_bdd): bool
{
    return password_verify($mdp_clair, $hash_bdd);
}

/**
 * Récupère le hash du mot de passe d'un utilisateur depuis la BDD.
 * Retourne null si l'utilisateur n'existe pas.
 */
function recuperer_hash_utilisateur(string $user): ?string
{
    require_once __DIR__ . '/../BDD/bdd.php';
    $connexion = getConnexion();
    $stmt = $connexion->prepare("SELECT PASSEWORD FROM PERSONNE WHERE NOM_P = :user");
    $stmt->execute([':user' => $user]);
    $row = $stmt->fetch();
    return $row ? $row['PASSEWORD'] : null;
}

/**
 * Vérifie le mot de passe ACTUEL de l'utilisateur connecté.
 * Redirige avec ?error=mdp_faux si le mot de passe est mauvais.
 */
function exiger_mot_de_passe_actuel(string $user, string $mdp_saisi): void
{
    $hash = recuperer_hash_utilisateur($user);
    if ($hash === null || !verifier_mot_de_passe($mdp_saisi, $hash)) {
        rediriger_erreur('mdp_faux');
    }
}

/* ==========================================================
   7) GESTION CENTRALE DES ERREURS PDO
   À utiliser dans un try/catch pour ne pas exposer la stack.
   ========================================================== */
function gerer_erreur_pdo(PDOException $e, string $contexte = ''): void
{
    error_log("Erreur PDO" . ($contexte ? " [$contexte]" : '') . " : " . $e->getMessage());
    rediriger_erreur('erreur_serveur');
}
?>