<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../BDD/bdd.php';
require_once __DIR__ . '/../security.php';

/* 1. Méthode HTTP */
exiger_methode_post();

/* 2. Vérification CSRF (le token a été généré dans forgot.php) */
verifier_csrf();

/* 3. Validation des champs obligatoires */
$reponse = valider_texte_obligatoire($_POST['reponse'] ?? null, 30);
$email   = valider_email($_POST['email']               ?? null);

/* 4. Vérifier que la réponse est correcte POUR CET email */
try {
    $connexion = getConnexion();
    $stmt = $connexion->prepare("SELECT 1 FROM PERSONNE 
                                 WHERE MAIL = :mail AND REPONSE = :reponse");
    $stmt->execute([':mail' => $email, ':reponse' => $reponse]);
    $ok = (bool)$stmt->fetchColumn();
    $connexion = null;
    if (!$ok) {
    header("Location: ../../html/page_login.php?error=mauvaise_reponse");
    exit;
}
} catch (PDOException $e) {
    gerer_erreur_pdo($e, 'reset_password_forgot');
}



/* 5. Si le nouveau mot de passe est fourni → on met à jour */
$new_mdp_brut = (string)($_POST['new_password'] ?? '');
$confirm      = (string)($_POST['confirm_password'] ?? '');

if ($new_mdp_brut !== '') {
    $new_mdp = valider_mot_de_passe($new_mdp_brut);
    exiger_mots_de_passe_identiques($new_mdp, $confirm);

    try {
        $connexion = getConnexion();
        $stmt = $connexion->prepare("UPDATE PERSONNE SET PASSEWORD = :mdp WHERE MAIL = :mail");
        $stmt->execute([
            ':mdp'  => hasher_mot_de_passe($new_mdp),
            ':mail' => $email,
        ]);
        $connexion = null;
    } catch (PDOException $e) {
        gerer_erreur_pdo($e, 'reset_password_update');
    }

    unset($_SESSION['forgot_email']);

    header("Location: ../../html/page_login.php?success=mdp_modifie");
    exit;
}

/* Étape 1 réussie (la réponse est bonne) mais pas de nouveau mdp
   → on affiche le formulaire de nouveau mot de passe.
   On regénère un CSRF pour ce formulaire. */
generer_csrf_token();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>FOKUS — Nouveau mot de passe</title>
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
        <h1 class="brand-tagline">Nouveau<br />mot de passe.</h1>
      </div>
    </aside>

    <main class="login-main">
      <div class="auth-form active">
        <div class="form-header">
          <h2>Réinitialisation 🔑</h2>
          <p>Choisissez un nouveau mot de passe sécurisé</p>
        </div>

        <form action="reset_password.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generer_csrf_token()) ?>" />
          <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>" />
          <input type="hidden" name="reponse" value="<?= htmlspecialchars($reponse) ?>"/>
          <div class="form-group">
            <label for="new_password">Nouveau mot de passe</label>
            <div class="password-wrapper">
              <input
                type="password"
                id="new_password"
                name="new_password"
                class="form-control"
                placeholder="Nouveau mot de passe"
                required
                minlength="8"
              />
              <button type="button" class="toggle-password">👁</button>
            </div>
          </div>

          <div class="form-group">
            <label for="confirm_password">Confirmer le mot de passe</label>
            <div class="password-wrapper">
              <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                class="form-control"
                placeholder="Répétez votre mot de passe"
                required
              />
              <button type="button" class="toggle-password">👁</button>
            </div>
          </div>

          <button type="submit" class="btn-gold btn-full">Modifier le mot de passe</button>
        </form>
      </div>
    </main>
  </div>

  <script src="../../js/main.js"></script>
</body>
</html>