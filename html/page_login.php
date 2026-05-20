<!doctype html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FOKUS — Connexion</title>
    <link rel="stylesheet" href="/css/global.css" />
    <link rel="stylesheet" href="/css/login.css" />
    <link rel="icon" type="image/png" href="/image/logo.png" />
  </head>
  <body class="login-body">

    <div class="login-container">

      <aside class="login-brand">
        <div class="brand-content">
          <a href="/html/page_de_garde.php" class="brand-logo">
            <img style="max-width:160px;width:100%" src="/image/marque.png" alt="Fokus" />
          </a>
          <h1 class="brand-tagline">Organisez.<br />Priorisez.<br />Accomplissez.</h1>
          <p class="brand-desc">
            FOKUS est votre gestionnaire de tâches personnel. Suivez vos objectifs,
            gérez vos catégories et visualisez votre progression en temps réel.
          </p>
          <div class="brand-features">
            <div class="feature-item"><span class="feature-icon">📋</span><span>Gestion de tâches avancée</span></div>
            <div class="feature-item"><span class="feature-icon">📊</span><span>Statistiques &amp; suivi de progression</span></div>
            <div class="feature-item"><span class="feature-icon">🗂️</span><span>Catégories personnalisées</span></div>
          </div>
        </div>
        <div class="brand-deco" aria-hidden="true"></div>
      </aside>

      <main class="login-main">
        <div class="auth-tabs" role="tablist">
          <button class="auth-tab active" data-tab="login" role="tab" aria-selected="true">Connexion</button>
          <button class="auth-tab" data-tab="register" role="tab" aria-selected="false">Inscription</button>
        </div>

        <!-- Formulaire Connexion -->
        <form class="auth-form active" id="form-login" action="/php/login/login.php" method="POST" novalidate>
          <div class="form-header">
            <h2>Bon retour 👋</h2>
            <p>Connectez-vous à votre espace FOKUS</p>
          </div>
          <div class="form-group">
            <label for="login-email">Adresse e-mail</label>
            <input type="email" id="login-email" name="email" class="form-control"
              placeholder="vous@exemple.com" required autocomplete="email" />
          </div>
          <div class="form-group">
            <label for="login-password">
              Mot de passe
              <a href="/php/login/get_mail.php" class="forgot-link">Mot de passe oublié ?</a>
            </label>
            <div class="password-wrapper">
              <input type="password" id="login-password" name="password" class="form-control"
                placeholder="••••••••" required autocomplete="current-password" />
              <button type="button" class="toggle-password" aria-label="Afficher">👁</button>
            </div>
          </div>
          <button type="submit" class="btn-gold btn-full">Se connecter</button>
        </form>

        <!-- Formulaire Inscription -->
        <form class="auth-form" id="form-register" action="/php/login/register.php" method="POST" novalidate>
          <div class="form-header">
            <h2>Créer un compte</h2>
            <p>Rejoignez FOKUS et commencez à vous organiser</p>
          </div>
          <div class="form-group">
            <label for="reg-username">Nom d'utilisateur</label>
            <input type="text" id="reg-username" name="username" class="form-control"
              placeholder="votre_pseudo" required minlength="3" maxlength="30" autocomplete="username" />
          </div>
          <div class="form-group">
            <label for="reg-email">Adresse e-mail</label>
            <input type="email" id="reg-email" name="email" class="form-control"
              placeholder="vous@exemple.com" required autocomplete="email" />
          </div>
          <div class="form-group">
            <label for="reg-password">Mot de passe</label>
            <div class="password-wrapper">
              <input type="password" id="reg-password" name="password" class="form-control"
                placeholder="Min. 8 caractères" required minlength="8" autocomplete="new-password" />
              <button type="button" class="toggle-password" aria-label="Afficher">👁</button>
            </div>
           
          </div>
          <div class="form-group">
            <label for="reg-confirm">Confirmer le mot de passe</label>
            <div class="password-wrapper">
              <input type="password" id="reg-confirm" name="confirm_password" class="form-control"
                placeholder="Répétez votre mot de passe" required autocomplete="new-password" />
              <button type="button" class="toggle-password" aria-label="Afficher">👁</button>
            </div>
          </div>
          <div class="form-group">
            <label for="forgot_mdp">Récupération de mot de passe</label>
            <select name="question_secrete" class="form-control">
              <option value="">-- Choisissez une question --</option>
              <option value="1">Quel est le prénom de votre mère ?</option>
              <option value="2">Quel est le nom de votre animal de compagnie ?</option>
              <option value="3">Dans quelle ville êtes-vous né(e) ?</option>
              <option value="4">Quel est le titre de votre film préféré ?</option>
            </select>
          </div>
          <div class="form-group">
            <label for="reponse">Réponse à la question</label>
            <input type="text" id="reponse" name="reponse" class="form-control"
              placeholder="Réponse à la question secrète" required />
          </div>
          <div class="form-check">
            <input type="checkbox" id="terms" name="terms"  value="1" required />
            <label for="terms">
              J'accepte les <a href="/php/login/terms.php" class="gold-link">conditions d'utilisation</a>
            </label>
          </div>
          <button type="submit" class="btn-gold btn-full">Créer mon compte</button>
        </form>
      </main>
    </div>

    <script src="/js/main.js"></script>
    <script src="/js/errors.js"></script>
    <script src="/js/success.js"></script>
  </body>
</html>