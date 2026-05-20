<?php require_once __DIR__ . '/../php/session/data_user.php'; ?>
<?php require_once __DIR__ . '/../php/session/statistiques.php'; ?>
<!doctype html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FOKUS — Profil</title>
    <link rel="stylesheet" href="/css/global.css" />
    <link rel="stylesheet" href="/css/profil.css" />
    <link rel="icon" type="image/png" href="/image/logo.png" />
  </head>
  <body>

    <?php require_once __DIR__ . '/util_html_elem/nav_bar.php'; ?>
    <?php require_once __DIR__ . '/util_html_elem/header.php'; ?>

      <main class="app-main">
        <h1 class="page-title">Mon Profil</h1>
        <p class="page-subtitle">Gérez vos informations personnelles et vos objectifs</p>

        <div class="profil-grid">
          <div>
            <div class="profil-identity">
              <div class="profil-banner"></div>
              <div class="profil-avatar-wrap">
                <div class="profil-avatar" onclick="openModal('modal-photo-profil')" style="cursor:pointer;">
                  <?php if ($photo_profil): ?>
                    <img src="<?= htmlspecialchars($photo_profil) ?>"
                        alt="Photo de profil"
                        class="avatar-img" />
                  <?php else: ?>
                    <div><?= htmlspecialchars($first_letter) ?></div>
                  <?php endif; ?>
                </div>
                <div class="profil-name"><?= htmlspecialchars($username) ?></div>
                <div class="profil-email"><?= htmlspecialchars($mail) ?></div>
                <div class="profil-since">Membre depuis : <?= htmlspecialchars($signin_date) ?></div>
              </div>
              <button class="quick-action" onclick="openModal('modal-modify-profil')">
                <span class="qa-icon">✏️</span> Modifier le profil
              </button>
              <div class="profil-mini-stats">
                <div class="mini-stat">
                  <span class="mini-stat-value"><?= $nb_taches ?></span>
                  <span class="mini-stat-label">Tâches totales</span>
                </div>
                <div class="mini-stat">
                  <span class="mini-stat-value"><?= $nb_categories ?></span>
                  <span class="mini-stat-label">Catégories</span>
                </div>
                <div class="mini-stat">
                  <span class="mini-stat-value" style="color:var(--status-done)"><?= $nb_taches_termines ?></span>
                  <span class="mini-stat-label">Terminées</span>
                </div>
                <div class="mini-stat">
                  <span class="mini-stat-value"><?= $nb_taches_en_cours ?></span>
                  <span class="mini-stat-label">En cours</span>
                </div>
              </div>
            </div>
          </div>

          <div class="profil-right">
            <div class="reinit-card">
              <div class="reinit-text">
                <h4>⚠️ Réinitialiser mes tâches</h4>
                <p>Cette action supprimera toutes vos tâches de façon irréversible.</p>
              </div>
              <button class="btn-danger" onclick="SuppressionTacheAll()">Réinitialiser</button>
            </div>
            <div class="objectifs-card">
              <div class="objectifs-header">
                <div class="objectifs-title">🎯 Mes Objectifs</div>
                <div style="display: flex; gap: 6px; align-items: center;">
                  <button class="btn-add-obj" onclick="openModal('modal-add-objectif')" title="Ajouter">＋</button>
                  <button class="btn-del-obj" onclick="SuppressionObjectifAll()" title="Supprimer tous">🗑</button>
                </div>
              </div>
              <div class="objectifs-list" id="objectifs-list">
                <?php if (empty($objectifs)): ?>
                  <div class="objectif-item">
                    <span class="obj-text" style="opacity:0.5">Aucun objectif pour l'instant.</span>
                  </div>
                <?php else: ?>
                  <?php foreach ($objectifs as $obj): ?>
                    <div class="objectif-item">
                      <span class="obj-bullet"></span>
                      <span class="obj-text"><?= htmlspecialchars($obj['TITRE_O']) ?></span>
                      <button class="obj-delete"
                        onclick="ouvrirEditObjectif('<?= htmlspecialchars($obj['TITRE_O'], ENT_QUOTES) ?>')"
                        title="Modifier">
                        ✏️
                      </button>
                      <button class="obj-delete"
                        onclick="SuppressionObjectif('<?= htmlspecialchars($obj['TITRE_O'], ENT_QUOTES) ?>')"
                        title="Supprimer">
                        ✕
                      </button>
                    </div>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div><!-- /.app-layout -->

    <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

    <script src="/js/main.js" defer></script>
    <script src="/js/edit.js" defer></script>
    <script src="/js/delete.js" defer></script>
    <script src="/js/success.js" defer></script>
    <script src="/js/errors.js" defer></script>
    <?php include __DIR__ . '/../php/modals.php'; ?>
  </body>
</html>