<!doctype html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/global.css" />
    <link rel="stylesheet" href="../css/page_de_garde.css" />
    <link rel="icon" type="image/png" href="../image/logo.png" />
    <?php include __DIR__ . '/../php/modals.php'; ?>
          <script src="/js/main.js" defer></script>
    <script src="/js/calendar.js"></script>
    <script src="/js/delete.js" defer></script>
    <script src="/js/success.js" defer></script>
    <script src="/js/errors.js" defer></script>
    <script src="/js/edit.js" defer></script>
    <title>Fokus</title>
  </head>
  <body>
    <header>
      <a href="#" class="header-logo">
        <img style="width:100%" src="../image/marque.png" alt="Fokus" class="header-image" />
      </a>
    </header>

    <div class="hero">
      <!-- Colonne gauche : texte de présentation -->
      <div class="hero-left">
        <p class="hero-eyebrow">Application de gestion de tâches</p>

        <h1 class="hero-title">
          Bienvenue sur<br />
          <span>FOKUS</span>
        </h1>

        <div class="hero-divider"></div>

        <p class="hero-desc">
          Fokus est une application conçue pour t'aider à rester concentré sur
          ce qui compte vraiment. Organise tes projets, priorise tes objectifs
          et suis ta progression en toute simplicité.
        </p>
        <p class="hero-desc">
          Que ce soit pour tes études, ton travail ou tes projets personnels,
          Fokus transforme tes idées en actions concrètes.
        </p>

        <div class="hero-cta">
          <a href="page_login.php" class="btn-gold">Commencer maintenant</a>
        </div>
      </div>

      <!-- Colonne droite : logo marque + slogan -->
      <div class="hero-right">
        <img src="../image/marque.png" alt="Fokus" class="hero-image" />

        <div class="hero-slogan">
          <p class="hero-slogan-text">
            Moins de <em> distractions</em>,<br />
            plus d'<em>action</em>.
          </p>
        </div>
      </div>
    </div>
  </body>
</html>
