<?php
require_once __DIR__ . '/../php/session/data_user.php';
require_once __DIR__ . '/../php/session/categorie.php';
$cal_mode = 'agenda';
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>FOKUS — Agenda</title>
  <link rel="stylesheet" href="/css/global.css" />
  <link rel="stylesheet" href="/css/agenda.css" />
  <link rel="icon" type="image/png" href="/image/logo.png" />
  <script src="/js/calendar.js"></script>
  <script src="/js/main.js" defer></script>
  <script src="/js/errors.js" defer></script>
  <script src="/js/success.js" defer></script>
  <?php include __DIR__ . '/../php/modals.php'; ?>
</head>
<body>

  <?php require_once __DIR__ . '/util_html_elem/nav_bar.php'; ?>
  <?php require_once __DIR__ . '/util_html_elem/header.php'; ?>

  <main class="app-main">

    <div class="agenda-header-bar">
      <div>
        <h1 class="page-title">Agenda</h1>
        <p class="page-subtitle">Visualisez vos tâches par date limite</p>
      </div>
      <div class="cat-page-actions">
        <button class="btn-outline" onclick="openModal('modal-add-categorie')">+ Nouvelle catégorie</button>
        <button class="btn-gold" onclick="openModal('modal-add-task')">+ Nouvelle tâche</button>
      </div>
    </div>

    <div class="agenda-layout">

      <!-- Col 1 : Calendrier -->
      <div class="agenda-left">
        <?php include __DIR__ . '/util_html_elem/calendar.php'; ?>
      </div>

      <!-- Col 2 : Tâches du jour -->
      <div class="agenda-day-col">
        <div class="agenda-day-detail" id="agenda-day-detail">
          <div class="agenda-detail-title" id="agenda-detail-title">📅 Tâches du jour</div>
          <div class="agenda-detail-list" id="agenda-detail-list">
            <div style="color:var(--text-muted);font-size:0.78rem;padding:8px 0;text-align:center">
              Cliquez sur un jour du calendrier
            </div>
          </div>
        </div>
      </div>

      <!-- Col 3 : Deadlines proches -->
      <div class="agenda-middle">
        <div class="agenda-alerts">
          <div class="alert-title">⚠️ Deadlines proches</div>
          <div class="agenda-alerts-list">
            <?php if (!empty($taches_urgentes)): ?>
              <?php foreach ($taches_urgentes as $t): ?>
                <div class="agenda-task-item">
                  <div class="agenda-task-left">
                    <span class="agenda-task-dot" style="background:<?= $STATUT_COLORS[$t['STATUT']] ?? 'var(--gold)' ?>"></span>
                    <div class="agenda-task-name"><?= htmlspecialchars($t['NOM_T']) ?></div>
                  </div>
                  <div class="agenda-task-right">
                    <span class="agenda-task-date">📅 <?= $t['LIMIT_DATE'] ?></span>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div style="color:var(--text-muted);font-size:0.78rem;padding:8px 0;text-align:center">
                Aucune deadline proche
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Col 4 : Toutes les deadlines -->
      <div class="agenda-right">
        <div class="agenda-all-tasks">
          <div class="section-title">📋 Toutes les deadlines</div>

          <?php if (empty($toutes_taches_agenda)): ?>
            <div style="color:var(--text-muted);font-size:0.82rem;padding:8px 0">Aucune deadline.</div>
          <?php else: ?>
            <?php
            $mois_affiche = '';
            foreach ($toutes_taches_agenda as $t):
              $mois_label = strtr(date('F Y', strtotime($t['LIMIT_DATE'])), $MOIS_FR);
              $is_past    = strtotime($t['LIMIT_DATE']) < strtotime(date('Y-m-d')) && $t['STATUT'] !== 'termine';
              $color      = $STATUT_COLORS[$t['STATUT']] ?? 'var(--gold)';
            ?>
              <?php if ($mois_label !== $mois_affiche): ?>
                <div class="agenda-month-label"><?= $mois_label ?></div>
                <?php $mois_affiche = $mois_label; ?>
              <?php endif; ?>

              <div class="agenda-task-item <?= $is_past ? 'past' : '' ?>">
                <div class="agenda-task-left">
                  <span class="agenda-task-dot" style="background:<?= $color ?>"></span>
                  <div>
                    <div class="agenda-task-name"><?= htmlspecialchars($t['NOM_T']) ?></div>
                    <?php if ($t['NOM_C']): ?>
                      <div class="agenda-task-cat" style="color:<?= htmlspecialchars($t['COLOR_C']) ?>">
                        <?= htmlspecialchars($t['NOM_C']) ?>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
                <div class="agenda-task-right">
                  <span class="agenda-task-statut" style="background:<?= $color ?>22;color:<?= $color ?>">
                    <?= $STATUT_LABELS[$t['STATUT']] ?>
                  </span>
                  <span class="agenda-task-date <?= $is_past ? 'overdue' : '' ?>">
                    <?= date('d/m', strtotime($t['LIMIT_DATE'])) ?>
                  </span>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </main>
  </div>

  <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

  <script>
    const toutesDeadlines = <?= json_encode($toutes_taches_agenda) ?>;
    const tachesParDate   = <?= json_encode($taches_par_date) ?>;
    const statutColors    = <?= json_encode($STATUT_COLORS) ?>;
    const statutLabels    = <?= json_encode($STATUT_LABELS) ?>;
    const MOIS_NOMS       = ['','Janvier','Février','Mars','Avril','Mai','Juin',
                             'Juillet','Août','Septembre','Octobre','Novembre','Décembre'];

    function afficherTachesJour(jour, mois, annee) {
      const dateKey = `${annee}-${String(mois).padStart(2,'0')}-${String(jour).padStart(2,'0')}`;
      const taches  = tachesParDate[dateKey] || [];
      const titre   = document.getElementById('agenda-detail-title');
      const liste   = document.getElementById('agenda-detail-list');

      titre.textContent = `📅 Tâches du ${jour} ${MOIS_NOMS[mois]}`;
      liste.innerHTML   = taches.length
        ? taches.map(t => `
            <div class="agenda-task-item">
              <div class="agenda-task-left">
                <span class="agenda-task-dot" style="background:${statutColors[t.STATUT] || 'var(--gold)'}"></span>
                <div class="agenda-task-name">${t.NOM_T}</div>
              </div>
              <span class="agenda-task-statut" style="background:${statutColors[t.STATUT]}22;color:${statutColors[t.STATUT]}">
                ${statutLabels[t.STATUT]}
              </span>
            </div>`).join('')
        : '<div style="color:var(--text-muted);font-size:0.78rem;padding:8px 0;text-align:center">Aucune tâche ce jour.</div>';
    }

    initCalendar({
      datesTaches:   <?= json_encode($dates_taches) ?>,
      titleId:       'cal-title',
      gridId:        'calendar-days',
      prevId:        'cal-prev',
      nextId:        'cal-next',
      dayCls:        'agenda-day',
      onDayClick:    (jour, mois, annee) => afficherTachesJour(jour, mois, annee),
    });
  </script>
</body>
</html>