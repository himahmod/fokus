<?php require_once __DIR__ . '/../php/session/data_user.php'; ?>
<?php require_once __DIR__ . '/../php/session/statistiques.php'; ?>
<!doctype html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FOKUS — Statistiques</title>
    <link rel="stylesheet" href="/css/global.css" />
    <link rel="stylesheet" href="/css/statistiques.css" />
    <link rel="icon" type="image/png" href="/image/logo.png" />
    <?php include __DIR__ . '/../php/modals.php'; ?>
  </head>
  <body>

    <?php require_once __DIR__ . '/util_html_elem/nav_bar.php'; ?>
    <?php require_once __DIR__ . '/util_html_elem/header.php'; ?>

    <main class="app-main">
      <h1 class="page-title">Statistiques</h1>
      <p class="page-subtitle">Analyse complète de vos tâches et de votre activité</p>

      <div class="stats-cards-grid">
        <div class="stat-card">
          <span class="stat-card-icon">🗂️</span>
          <span class="stat-card-value"><?= $nb_categories ?></span>
          <span class="stat-card-label">Catégories</span>
          <div class="stat-card-sub">Nombre de catégories créées</div>
        </div>
        <div class="stat-card">
          <span class="stat-card-icon">📋</span>
          <span class="stat-card-value"><?= $nb_taches ?></span>
          <span class="stat-card-label">Tâches totales</span>
          <div class="stat-card-sub">Toutes tâches confondues</div>
        </div>
        <div class="stat-card todo">
          <span class="stat-card-icon">⏳</span>
          <span class="stat-card-value"><?= $nb_sans_categories ?></span>
          <span class="stat-card-label">Sans catégorie</span>
          <div class="stat-card-sub">Tâches non classées</div>
        </div>
        <div class="stat-card">
          <span class="stat-card-icon">🔄</span>
          <span class="stat-card-value"><?= $nb_taches_en_cours ?></span>
          <span class="stat-card-label">En cours</span>
          <div class="stat-card-sub">Actuellement en traitement</div>
        </div>
        <div class="stat-card urgent">
          <span class="stat-card-icon">⚠️</span>
          <span class="stat-card-value"><?= $nb_taches_urgent ?></span>
          <span class="stat-card-label">Urgentes</span>
          <div class="stat-card-sub">Marquées comme urgentes</div>
        </div>
        <div class="stat-card done">
          <span class="stat-card-icon">✅</span>
          <span class="stat-card-value"><?= $nb_taches_termines ?></span>
          <span class="stat-card-label">Terminées</span>
          <div class="stat-card-sub">Complétées avec succès</div>
        </div>
        <div class="stat-card">
          <span class="stat-card-icon">📁</span>
          <span class="stat-card-value"><?= $nb_tache_avg ?></span>
          <span class="stat-card-label">Par catégorie</span>
          <div class="stat-card-sub">Moyenne par catégorie</div>
        </div>
        <div class="stat-card todo">
          <span class="stat-card-icon">📌</span>
          <span class="stat-card-value"><?= $nb_taches_a_faire ?></span>
          <span class="stat-card-label">À faire</span>
          <div class="stat-card-sub">En attente de traitement</div>
        </div>
      </div>

      <div class="stats-bottom-grid">

        <div class="categories-breakdown">
          <div class="section-title">Répartition par catégorie</div>

          <?php if (empty($repartition_categories)): ?>
            <p style="color: var(--text-muted); font-size: 0.85rem; padding: 20px 0;">Aucune catégorie créée.</p>
          <?php else: ?>
            <?php foreach ($repartition_categories as $cat):
              $pct = $nb_taches > 0 ? round(($cat['nb'] / $nb_taches) * 100) : 0;
            ?>
              <div class="cat-bar-item">
                <div class="cat-bar-header">
                  <span class="cat-bar-name">
                    <span class="cat-bar-dot" style="background: <?= htmlspecialchars($cat['COLOR_C']) ?>"></span>
                    <?= htmlspecialchars($cat['NOM_C']) ?>
                  </span>
                  <span class="cat-bar-count"><?= $cat['nb'] ?> tâche<?= $cat['nb'] > 1 ? 's' : '' ?> — <?= $pct ?>%</span>
                </div>
                <div class="cat-bar-track">
                  <div class="cat-bar-fill" style="width: <?= $pct ?>%; background: <?= htmlspecialchars($cat['COLOR_C']) ?>"></div>
                </div>
              </div>
            <?php endforeach; ?>

            <?php if ($nb_sans_categories > 0):
              $pct_sans = $nb_taches > 0 ? round(($nb_sans_categories / $nb_taches) * 100) : 0;
            ?>
              <div class="cat-bar-item">
                <div class="cat-bar-header">
                  <span class="cat-bar-name">
                    <span class="cat-bar-dot" style="background: var(--text-muted)"></span>
                    Sans catégorie
                  </span>
                  <span class="cat-bar-count"><?= $nb_sans_categories ?> tâche<?= $nb_sans_categories > 1 ? 's' : '' ?> — <?= $pct_sans ?>%</span>
                </div>
                <div class="cat-bar-track">
                  <div class="cat-bar-fill" style="width: <?= $pct_sans ?>%; opacity: 0.4"></div>
                </div>
              </div>
            <?php endif; ?>
          <?php endif; ?>
        </div>

        <aside class="statistique-aside">
          <div class="stats-summary">
            <div class="stats-summary-header">📈 Vue d'ensemble</div>
            <div class="stats-grid">
              <div class="stat-cell">
                <span class="stat-cell-value"><?= $nb_taches ?></span>
                <span class="stat-cell-label">Total tâches</span>
              </div>
              <div class="stat-cell">
                <span class="stat-cell-value"><?= $nb_taches_en_cours ?></span>
                <span class="stat-cell-label">En cours</span>
              </div>
              <div class="stat-cell">
                <span class="stat-cell-value" style="color:var(--status-todo)"><?= $nb_taches_a_faire ?></span>
                <span class="stat-cell-label">À faire</span>
              </div>
              <div class="stat-cell">
                <span class="stat-cell-value" style="color:var(--status-done)"><?= $nb_taches_termines ?></span>
                <span class="stat-cell-label">Terminées</span>
              </div>
            </div>
          </div>

          <div class="progress-card card">
            <div class="section-title">Progression globale</div>
            <div style="font-size: 0.83rem; color: var(--text-muted); margin-bottom: 10px;">
              Tâches terminées sur le total
            </div>
            <div class="progress-bar-wrap">
              <div class="progress-bar-fill" style="width: <?= $pct_progression ?>%"></div>
            </div>
            <div class="progress-label">
              <span><?= $nb_taches_termines ?> terminée<?= $nb_taches_termines > 1 ? 's' : '' ?></span>
              <span><?= $pct_progression ?>%</span>
            </div>
          </div>
        </aside>

      </div>
    </main>
    </div><!-- /.app-layout -->

    <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

    <script src="/js/main.js" defer></script>
  </body>
</html>