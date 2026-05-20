<!doctype html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FOKUS — Récupération</title>
    <link rel="stylesheet" href="../../css/global.css" />
    <link rel="stylesheet" href="../../css/login.css" />
    <link rel="icon" type="image/png" href="../../image/logo.png" />
  </head>
  <body class="login-body">

    <div class="login-container">

      <!-- Panneau gauche -->
      <aside class="login-brand">
        <div class="brand-content">
          <a href="../../html/page_de_garde.php" class="brand-logo">
            <img src="../../image/marque.png" alt="Fokus" />
          </a>
          <h1 class="brand-tagline">
            Récupération<br />de compte.
          </h1>
          <p class="brand-desc">
            Entrez votre adresse e-mail pour récupérer votre compte FOKUS.
          </p>
        </div>
        <div class="brand-deco" aria-hidden="true"></div>
      </aside>

      <!-- Panneau droit -->
      <main class="login-main">
        <div class="auth-form active">
          <div class="form-header">
            <h2>Mot de passe oublié 🔑</h2>
            <p>Entrez votre e-mail pour retrouver votre compte</p>
          </div>

          <form action="forgot.php" method="POST" novalidate>
            <div class="form-group">
              <label for="login-email">Adresse e-mail</label>
              <input
                type="email"
                id="login-email"
                name="email"
                class="form-control"
                placeholder="vous@exemple.com"
                required
                autocomplete="email"
              />
            </div>

            <button type="submit" class="btn-gold btn-full">Continuer</button>

            <p style="text-align:center; margin-top: 16px; font-size: 0.85rem; color: var(--text-muted);">
              <a href="../../html/page_login.php" class="gold-link">← Retour à la connexion</a>
            </p>
          </form>
        </div>
      </main>

    </div>

    <!-- Modals -->

<?php include __DIR__ . '/../modals.php'; ?>
    <script src="../../js/main.js"></script>
    <script src="../../js/errors.js"></script>
  </body>
</html>