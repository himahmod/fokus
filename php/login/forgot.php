<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../BDD/bdd.php';
require_once __DIR__ . '/../security.php';

/* 1. Validation de l'email
   Note : pas de exiger_methode_post() ici car cette page peut
   aussi être rechargée. On valide simplement l'email reçu. */
$email = valider_email($_POST['email'] ?? null);

/* 2. Recherche de l'utilisateur par mail */
try {
    $connexion = getConnexion();
    $stmt = $connexion->prepare("SELECT QUESTION, REPONSE FROM PERSONNE WHERE MAIL = :mail");
    $stmt->execute([':mail' => $email]);
    $qst_rep = $stmt->fetch();
    $connexion = null;
} catch (PDOException $e) {
    gerer_erreur_pdo($e, 'forgot');
}

if (!$qst_rep) {
    // Email non trouvé → on redirige avec une erreur générique
    // (volontairement floue pour ne pas révéler quels emails existent)
    header("Location: ../../html/page_login.php?error=email_invalide");
    exit;
}

$qst = (int)$qst_rep['QUESTION'];

$questions = [
    1 => "Quel est le prénom de votre mère ?",
    2 => "Quel est le nom de votre animal de compagnie ?",
    3 => "Dans quelle ville êtes-vous né(e) ?",
    4 => "Quel est le titre de votre film préféré ?",
];

if (!isset($questions[$qst])) {
    // Question secrète inconnue (incohérence en BDD)
    header("Location: ../../html/page_login.php?error=email_invalide");
    exit;
}

$question_texte = $questions[$qst];
$_SESSION['forgot_email'] = $email;

// On génère un token CSRF pour le formulaire suivant
generer_csrf_token();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>FOKUS — Mot de passe oublié</title>
  <link rel="stylesheet" href="../../css/global.css">
  <link rel="stylesheet" href="../../css/login.css">
</head>
<body class="login-body">
  <div class="login-container">
    <aside class="login-brand">
      <div class="brand-content">
        <a href="../../html/page_de_garde.php" class="brand-logo">
          <img src="../../image/marque.png" alt="Fokus" />
        </a>
        <h1 class="brand-tagline">Récupération<br />de compte.</h1>
      </div>
    </aside>

    <main class="login-main">
      <div class="auth-form active">
        <div class="form-header">
          <h2>Question secrète 🔐</h2>
          <p>Répondez à votre question pour réinitialiser votre mot de passe</p>
        </div>

        <form action="reset_password.php" method="POST">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>" />
          <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>" />

          <div class="form-group">
            <label><?= htmlspecialchars($question_texte) ?></label>
            <input
              type="text"
              name="reponse"
              class="form-control"
              placeholder="Votre réponse..."
              required
            />
          </div>

          <button type="submit" class="btn-gold btn-full">Vérifier</button>
        </form>
      </div>
    </main>
  </div>
</body>
</html>