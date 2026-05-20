<?php
/* ==========================================================
   FOKUS — calendar.php
   Variables attendues avant l'include :
     $dates_taches  — array de strings "Y-m-d"
     $cal_mode      — 'dashboard' (défaut) ou 'agenda'
   ========================================================== */

$mode = $cal_mode ?? 'dashboard';
$agenda = $mode === 'agenda';
?>

<div class="<?= $agenda ? 'agenda-calendar' : 'calendar-card' ?>">

  <div class="<?= $agenda ? 'agenda-cal-header' : 'calendar-header' ?>">
    <button class="cal-nav-btn" id="cal-prev">‹</button>
    <span class="<?= $agenda ? 'agenda-cal-title' : 'calendar-title' ?>" id="cal-title"></span>
    <button class="cal-nav-btn" id="cal-next">›</button>
  </div>

  <?php if (!$agenda): ?><div class="calendar-grid"><?php endif; ?>

    <div class="<?= $agenda ? 'agenda-days-header' : 'cal-days-header' ?>">
      <?php foreach (['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'] as $j): ?>
        <div class="<?= $agenda ? 'agenda-day-name' : 'cal-day-name' ?>"><?= $j ?></div>
      <?php endforeach; ?>
    </div>

    <div class="<?= $agenda ? 'agenda-days' : 'cal-days' ?>" id="calendar-days"></div>

  <?php if (!$agenda): ?></div><?php endif; ?>

  <div class="cal-legend">
    <div class="cal-legend-item">
      <div class="cal-legend-dot" style="background:var(--gold)"></div> Aujourd'hui
    </div>
    <div class="cal-legend-item">
      <div class="cal-legend-dot" style="background:var(--gold);opacity:0.5"></div> Tâche planifiée
    </div>
  </div>

</div>