<?php require_once __DIR__ . '/../php/session/data_user.php'; ?>
<?php require_once __DIR__ . '/../php/session/categorie.php'; ?>
<?php require_once __DIR__ . '/../php/session/statistiques.php'; ?>
<!doctype html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FOKUS — Mes Tâches</title>
    <link rel="stylesheet" href="/css/global.css" />
    <link rel="stylesheet" href="/css/dashboard.css" />
    <link rel="stylesheet" href="/css/mes-taches.css" />
    <link rel="icon" type="image/png" href="/image/logo.png" />
    <script src="/js/main.js" defer></script>
    <script src="/js/delete.js" defer></script>
    <script src="/js/edit.js" defer></script>
    <script src="/js/errors.js" defer></script>
    <script src="/js/success.js" defer></script>
    <?php include __DIR__ . '/../php/modals.php'; ?>
  </head>
  <body>
    <?php require_once __DIR__ . '/util_html_elem/nav_bar.php'; ?>
    <?php require_once __DIR__ . '/util_html_elem/header.php'; ?>

    <main class="app-main">

      <!-- Header avec titre + boutons -->
     <div class="taches-header-bar">
  <div>
    <h1 class="page-title">Mes Tâches</h1>
    <p class="page-subtitle">Organisez et suivez vos tâches par statut</p>
  </div>

  <div class="taches-toolbar-left">
    <div class="filter-tabs" role="tablist">
      <button class="filter-tab active" data-filter="all">Toutes</button>
      <button class="filter-tab" data-filter="urgent">Urgentes</button>
      <button class="filter-tab" data-filter="en_cours">En cours</button>
      <button class="filter-tab" data-filter="a_faire">À faire</button>
      <button class="filter-tab" data-filter="termine">Terminées</button>
    </div>
    <div class="search-input-wrap">
      <span class="search-icon">🔍</span>
      <input type="search" class="search-input" id="search-tasks"
        placeholder="Rechercher une tâche..." aria-label="Rechercher une tâche" />
    </div>
  </div>

  <div class="taches-toolbar-right">
    <button class="btn-outline" onclick="openModal('modal-add-categorie')">+ Nouvelle catégorie</button>
    <button class="btn-gold" onclick="openModal('modal-add-task')">+ Nouvelle tâche</button>
  </div>
</div>

      <div class="kanban-grid" id="kanban-grid">

        <!-- Colonne : En cours -->
        <section class="kanban-col task-column" data-col="en_cours">
          <div class="task-column-header">
            <div class="task-column-title">
              <span class="task-col-dot dot-inprog"></span> En cours
            </div>
            <div class="kanban-col-actions">
              <span class="task-col-count"><?= count($taches_en_cours) ?> tâche<?= count($taches_en_cours) > 1 ? 's' : '' ?></span>
              <button class="btn-col-action del" title="Vider la colonne" onclick="SuppressionTachesCol('en_cours')">🗑</button>
            </div>
          </div>
          <div class="task-list">
            <?php foreach ($taches_en_cours as $tache):
              $is_urgent   = !empty($tache['URGENT']);
              $t_nom       = htmlspecialchars(json_encode($tache['NOM_T']), ENT_QUOTES);
              $t_cat       = htmlspecialchars(json_encode($tache['CATEGORIE_T']), ENT_QUOTES);
              $t_date      = htmlspecialchars(json_encode($tache['LIMIT_DATE']), ENT_QUOTES);
              $t_stat      = htmlspecialchars(json_encode(trim($tache['STATUT'])), ENT_QUOTES);
              $t_desc      = htmlspecialchars(json_encode($tache['DESC_T'] ?? ''), ENT_QUOTES);
              $t_nom_cat   = htmlspecialchars($tache['NOM_CAT'] ?? '');
              $t_color_cat = htmlspecialchars($tache['COLOR_CAT'] ?? '');
            ?>
              <div class="task-item<?= $is_urgent ? ' urgent' : '' ?>"
                   data-id="<?= $tache['ID_T'] ?>"
                   data-nom="<?= htmlspecialchars($tache['NOM_T']) ?>"
                   data-desc="<?= htmlspecialchars($tache['DESC_T'] ?? '') ?>"
                   data-statut="<?= htmlspecialchars(trim($tache['STATUT'])) ?>"
                   data-date="<?= htmlspecialchars($tache['LIMIT_DATE'] ?? '') ?>"
                   data-cat="<?= $t_nom_cat ?>"
                   data-cat-color="<?= $t_color_cat ?>"
                   data-urgent="<?= $is_urgent ? '1' : '0' ?>"
                   onclick="ouvrirViewTache(this)">
                <div class="task-item-body">
                  <div class="task-item-title"><?= htmlspecialchars($tache['NOM_T']) ?></div>
                  <div class="task-item-meta">
                    <?php if ($tache['NOM_CAT']): ?>
                      <span class="task-item-cat" style="background-color: <?= $t_color_cat ?>;"><?= $t_nom_cat ?></span>
                    <?php endif; ?>
                    <?php if ($tache['LIMIT_DATE']): ?>
                      <span class="task-item-date">📅 <?= $tache['LIMIT_DATE'] ?></span>
                    <?php endif; ?>
                    <span class="badge badge-inprog">En cours</span>
                  </div>
                </div>
                <div class="task-item-actions" onclick="event.stopPropagation()">
                  <button class="task-action-btn urgent-btn<?= $is_urgent ? ' is-urgent' : '' ?>"
                          title="<?= $is_urgent ? 'Retirer urgent' : 'Marquer urgent' ?>"
                          onclick="toggleUrgent(this, <?= $tache['ID_T'] ?>)">
                    <?= $is_urgent ? '❕' : '❗' ?>
                  </button>
                  <button class="task-action-btn" title="Modifier" onclick="ouvrirEditTache(<?= $tache['ID_T'] ?>, <?= $t_nom ?>, <?= $t_cat ?>, <?= $t_date ?>, <?= $t_stat ?>, <?= $t_desc ?>)">✏️</button>
                  <button class="task-action-btn del" title="Supprimer" onclick="SuppressionTache(<?= $tache['ID_T'] ?>)">🗑</button>
                </div>
              </div>
            <?php endforeach; ?>
            <?php if (empty($taches_en_cours)): ?>
              <div class="task-empty">Aucune tâche en cours</div>
            <?php endif; ?>
          </div>
        
        </section>

        <!-- Colonne : À faire -->
        <section class="kanban-col task-column" data-col="a_faire">
          <div class="task-column-header">
            <div class="task-column-title">
              <span class="task-col-dot dot-todo"></span> À faire
            </div>
            <div class="kanban-col-actions">
              <span class="task-col-count"><?= count($taches_a_faire) ?> tâche<?= count($taches_a_faire) > 1 ? 's' : '' ?></span>
              <button class="btn-col-action del" title="Vider la colonne" onclick="SuppressionTachesCol('a_faire')">🗑</button>
            </div>
          </div>
          <div class="task-list">
            <?php foreach ($taches_a_faire as $tache):
              $is_urgent   = !empty($tache['URGENT']);
              $t_nom       = htmlspecialchars(json_encode($tache['NOM_T']), ENT_QUOTES);
              $t_cat       = htmlspecialchars(json_encode($tache['CATEGORIE_T']), ENT_QUOTES);
              $t_date      = htmlspecialchars(json_encode($tache['LIMIT_DATE']), ENT_QUOTES);
              $t_stat      = htmlspecialchars(json_encode(trim($tache['STATUT'])), ENT_QUOTES);
              $t_desc      = htmlspecialchars(json_encode($tache['DESC_T'] ?? ''), ENT_QUOTES);
              $t_nom_cat   = htmlspecialchars($tache['NOM_CAT'] ?? '');
              $t_color_cat = htmlspecialchars($tache['COLOR_CAT'] ?? '');
            ?>
              <div class="task-item<?= $is_urgent ? ' urgent' : '' ?>"
                   data-id="<?= $tache['ID_T'] ?>"
                   data-nom="<?= htmlspecialchars($tache['NOM_T']) ?>"
                   data-desc="<?= htmlspecialchars($tache['DESC_T'] ?? '') ?>"
                   data-statut="<?= htmlspecialchars(trim($tache['STATUT'])) ?>"
                   data-date="<?= htmlspecialchars($tache['LIMIT_DATE'] ?? '') ?>"
                   data-cat="<?= $t_nom_cat ?>"
                   data-cat-color="<?= $t_color_cat ?>"
                   data-urgent="<?= $is_urgent ? '1' : '0' ?>"
                   onclick="ouvrirViewTache(this)">
                <div class="task-item-body">
                  <div class="task-item-title"><?= htmlspecialchars($tache['NOM_T']) ?></div>
                  <div class="task-item-meta">
                    <?php if ($tache['NOM_CAT']): ?>
                      <span class="task-item-cat" style="background-color: <?= $t_color_cat ?>;"><?= $t_nom_cat ?></span>
                    <?php endif; ?>
                    <?php if ($tache['LIMIT_DATE']): ?>
                      <span class="task-item-date">📅 <?= $tache['LIMIT_DATE'] ?></span>
                    <?php endif; ?>
                    <span class="badge badge-todo">À faire</span>
                  </div>
                </div>
                <div class="task-item-actions" onclick="event.stopPropagation()">
                  <button class="task-action-btn urgent-btn<?= $is_urgent ? ' is-urgent' : '' ?>"
                          title="<?= $is_urgent ? 'Retirer urgent' : 'Marquer urgent' ?>"
                          onclick="toggleUrgent(this, <?= $tache['ID_T'] ?>)">
                    <?= $is_urgent ? '❕' : '❗' ?>
                  </button>
                  <button class="task-action-btn" title="Modifier" onclick="ouvrirEditTache(<?= $tache['ID_T'] ?>, <?= $t_nom ?>, <?= $t_cat ?>, <?= $t_date ?>, <?= $t_stat ?>, <?= $t_desc ?>)">✏️</button>
                  <button class="task-action-btn del" title="Supprimer" onclick="SuppressionTache(<?= $tache['ID_T'] ?>)">🗑</button>
                </div>
              </div>
            <?php endforeach; ?>
            <?php if (empty($taches_a_faire)): ?>
              <div class="task-empty">Aucune tâche à faire</div>
            <?php endif; ?>
          </div>
         
        </section>

        <!-- Colonne : Terminées -->
        <section class="kanban-col task-column" data-col="termine">
          <div class="task-column-header">
            <div class="task-column-title">
              <span class="task-col-dot dot-done"></span> Terminées
            </div>
            <div class="kanban-col-actions">
              <span class="task-col-count"><?= count($taches_termines) ?> tâche<?= count($taches_termines) > 1 ? 's' : '' ?></span>
              <button class="btn-col-action del" title="Vider la colonne" onclick="SuppressionTachesCol('termine')">🗑</button>
            </div>
          </div>
          <div class="task-list">
            <?php foreach ($taches_termines as $tache):
              $is_urgent   = !empty($tache['URGENT']);
              $t_nom       = htmlspecialchars(json_encode($tache['NOM_T']), ENT_QUOTES);
              $t_cat       = htmlspecialchars(json_encode($tache['CATEGORIE_T']), ENT_QUOTES);
              $t_date      = htmlspecialchars(json_encode($tache['LIMIT_DATE']), ENT_QUOTES);
              $t_stat      = htmlspecialchars(json_encode(trim($tache['STATUT'])), ENT_QUOTES);
              $t_desc      = htmlspecialchars(json_encode($tache['DESC_T'] ?? ''), ENT_QUOTES);
              $t_nom_cat   = htmlspecialchars($tache['NOM_CAT'] ?? '');
              $t_color_cat = htmlspecialchars($tache['COLOR_CAT'] ?? '');
            ?>
              <div class="task-item<?= $is_urgent ? ' urgent' : '' ?>"
                   data-id="<?= $tache['ID_T'] ?>"
                   data-nom="<?= htmlspecialchars($tache['NOM_T']) ?>"
                   data-desc="<?= htmlspecialchars($tache['DESC_T'] ?? '') ?>"
                   data-statut="<?= htmlspecialchars(trim($tache['STATUT'])) ?>"
                   data-date="<?= htmlspecialchars($tache['LIMIT_DATE'] ?? '') ?>"
                   data-cat="<?= $t_nom_cat ?>"
                   data-cat-color="<?= $t_color_cat ?>"
                   data-urgent="<?= $is_urgent ? '1' : '0' ?>"
                   onclick="ouvrirViewTache(this)">
                <div class="task-item-body">
                  <div class="task-item-title"><?= htmlspecialchars($tache['NOM_T']) ?></div>
                  <div class="task-item-meta">
                    <?php if ($tache['NOM_CAT']): ?>
                      <span class="task-item-cat" style="background-color: <?= $t_color_cat ?>;"><?= $t_nom_cat ?></span>
                    <?php endif; ?>
                    <?php if ($tache['LIMIT_DATE']): ?>
                      <span class="task-item-date">📅 <?= $tache['LIMIT_DATE'] ?></span>
                    <?php endif; ?>
                    <span class="badge badge-done">Terminée</span>
                  </div>
                </div>
                <div class="task-item-actions" onclick="event.stopPropagation()">
                  <button class="task-action-btn urgent-btn<?= $is_urgent ? ' is-urgent' : '' ?>"
                          title="<?= $is_urgent ? 'Retirer urgent' : 'Marquer urgent' ?>"
                          onclick="toggleUrgent(this, <?= $tache['ID_T'] ?>)">
                    <?= $is_urgent ? '❕' : '❗' ?>
                  </button>
                  <button class="task-action-btn" title="Modifier" onclick="ouvrirEditTache(<?= $tache['ID_T'] ?>, <?= $t_nom ?>, <?= $t_cat ?>, <?= $t_date ?>, <?= $t_stat ?>, <?= $t_desc ?>)">✏️</button>
                  <button class="task-action-btn del" title="Supprimer" onclick="SuppressionTache(<?= $tache['ID_T'] ?>)">🗑</button>
                </div>
              </div>
            <?php endforeach; ?>
            <?php if (empty($taches_termines)): ?>
              <div class="task-empty">Aucune tâche terminée</div>
            <?php endif; ?>
          </div>
          
        </section>

      </div>
    </main>
    </div><!-- /.app-layout -->

    <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

  </body>
</html>