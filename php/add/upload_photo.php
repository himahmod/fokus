<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../BDD/bdd.php';
require_once __DIR__ . '/../security.php';

/* ── 1. Authentification ── */
$user = exiger_utilisateur_connecte();

/* ── 2. Méthode & CSRF ── */
exiger_methode_post();
verifier_csrf();

/* ── 3. Vérification du fichier ── */
if (empty($_FILES['photo'])) {
    rediriger_erreur('champs_vides');
}

$erreurUpload = $_FILES['photo']['error'];

if ($erreurUpload === UPLOAD_ERR_INI_SIZE || $erreurUpload === UPLOAD_ERR_FORM_SIZE) {
    rediriger_erreur('photo_trop_lourde');
}

if ($erreurUpload !== UPLOAD_ERR_OK) {
    rediriger_erreur('erreur_serveur');
}

$file    = $_FILES['photo'];
$maxSize = 4 * 1024 * 1024; // 4 Mo
$typesOk = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

if ($file['size'] > $maxSize) {
    rediriger_erreur('photo_trop_lourde');
}

$finfo    = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($file['tmp_name']);

if (!in_array($mimeType, $typesOk, true)) {
    rediriger_erreur('photo_type_invalide');
}

/* ── 4. Encodage base64 ── */
$contenu = file_get_contents($file['tmp_name']);
if ($contenu === false) {
    rediriger_erreur('erreur_serveur');
}
$base64 = 'data:' . $mimeType . ';base64,' . base64_encode($contenu);

/* ── 5. Sauvegarde en BDD ── */
try {
    $connexion = getConnexion();
    $stmt = $connexion->prepare(
        "INSERT INTO PHOTO_PROFIL (UTILISATEUR, PHOTO)
         VALUES (:user, :photo)
         ON DUPLICATE KEY UPDATE PHOTO = :photo2, DATE_UPLOAD = NOW()"
    );
    $stmt->execute([
        ':user'   => $user,
        ':photo'  => $base64,
        ':photo2' => $base64,
    ]);
    $connexion = null;
} catch (PDOException $e) {
    gerer_erreur_pdo($e, 'upload_photo');
}

rediriger_succes('photo_updated');
?>