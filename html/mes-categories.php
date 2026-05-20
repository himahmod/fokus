<?php require_once __DIR__ . '/../php/session/data_user.php'; ?>
<?php require_once __DIR__ . '/../php/session/categorie.php'; ?>

<!doctype html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FOKUS — Mes Catégories</title>
    <link rel="stylesheet" href="/css/global.css" />
    <link rel="stylesheet" href="/css/mes-categories.css" />
    <link rel="icon" type="image/png" href="/image/logo.png" />
    <?php include __DIR__ . '/../php/modals.php'; ?>
  </head>
  <body>

    <?php require_once __DIR__ . '/util_html_elem/nav_bar.php'; ?>
    <?php require_once __DIR__ . '/util_html_elem/header.php'; ?>

      <main class="app-main">

        <div class="cat-page-header">
          <div>
            <h1 class="page-title">Mes Catégories</h1>
            <p class="page-subtitle">Organisez vos tâches par catégories</p>
          </div>
          <div class="cat-page-actions">
            <button class="btn-danger" onclick="SuppressionCategorieAll()">🗑 Tout supprimer</button>
            <button class="btn-gold" onclick="openModal('modal-add-categorie')">+ Nouvelle catégorie</button>
          </div>
        </div>

        <?php if (empty($categories)): ?>
          <div class="cat-empty">
            <div class="cat-empty-icon">🗂️</div>
            <p>Vous n'avez aucune catégorie pour l'instant.</p>
            <button class="btn-gold" onclick="openModal('modal-add-categorie')">+ Créer ma première catégorie</button>
          </div>

        <?php else: ?>
          <div class="cat-grid">
            <?php foreach ($categories as $cat):
              $sanitize  = fn($s) => str_replace(["\r\n", "\r", "\n"], ' ', trim($s ?? ''));
              $cat_nom   = htmlspecialchars(json_encode($sanitize($cat['NOM_C'])),   ENT_QUOTES);
              $cat_desc  = htmlspecialchars(json_encode($sanitize($cat['DESC_C'])),  ENT_QUOTES);
              $cat_color = htmlspecialchars(json_encode($sanitize($cat['COLOR_C'])), ENT_QUOTES);
            ?>
              <div class="cat-card">

                <div class="cat-card-header" style="border-top: 4px solid <?= htmlspecialchars($cat['COLOR_C']) ?>">
                  <div class="cat-card-title">
                    <span class="cat-color-dot" style="background: <?= htmlspecialchars($cat['COLOR_C']) ?>"></span>
                    <?= htmlspecialchars($cat['NOM_C']) ?>
                  </div>
                  <div class="cat-card-actions">
                    <button class="btn-col-action"
                      onclick="ouvrirEditCategorie(<?= $cat['ID_C'] ?>, <?= $cat_nom ?>, <?= $cat_desc ?>, <?= $cat_color ?>)"
                      title="Modifier">✏️</button>
                    <button class="btn-col-action del"
                      onclick="SuppressionCategorie(<?=$cat['ID_C'] ?>)"
                      title="Supprimer">🗑</button>
                  </div>
                </div>

                <?php if (empty($cat['DESC_C'])): ?>
                  <div class="cat-card-desc">Pas de description</div>
                <?php else: ?>
                <div class="cat-card-desc"><?= htmlspecialchars($cat['DESC_C']) ?></div>
                <?php endif; ?>
                <div class="cat-card-count">
                  <span class="cat-count-badge"><?= $cat['nb_taches'] ?> tâche<?= $cat['nb_taches'] > 1 ? 's' : '' ?></span>
                </div>

                <?php if (!empty($cat['taches'])): ?>
                  <div class="cat-task-list">
                    <?php foreach ($cat['taches'] as $tache): ?>
                      <div class="cat-task-item">
                        <span class="cat-task-dot status-<?= $tache['STATUT'] ?>"></span>
                        <span class="cat-task-name"><?= htmlspecialchars($tache['NOM_T']) ?></span>
                        <?php if ($tache['LIMIT_DATE']): ?>
                          <span class="cat-task-date"><?= $tache['LIMIT_DATE'] ?></span>
                        <?php endif; ?>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php else: ?>
                  <div class="cat-task-empty">Aucune tâche dans cette catégorie</div>
                <?php endif; ?>

                <div class="cat-card-footer">
                  <button class="btn-add-card" onclick="openModal('modal-add-task')">
                    <span>＋</span> Ajouter une tâche
                  </button>
                </div>

              </div><!-- /.cat-card -->
            <?php endforeach; ?>
          </div><!-- /.cat-grid -->
        <?php endif; ?>

      </main>
    </div><!-- /.app-layout -->

    <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

    <script src="/js/main.js" defer></script>
    <script src="/js/delete.js" defer></script>
    <script src="/js/errors.js" defer></script>
    <script src="/js/success.js" defer></script>
    <script src="/js/edit.js" defer></script>
  </body>
</html>