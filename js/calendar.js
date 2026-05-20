/* ==========================================================
   FOKUS — calendar.js
   Fonction commune pour dashboard et agenda.

   Usage dashboard :
     initCalendar({ datesTaches, titleId, gridId, prevId, nextId });

   Usage agenda (avec callback clic jour) :
     initCalendar({ ..., dayCls: 'agenda-day', onDayClick: (j,m,a) => {} });
   ========================================================== */
"use strict";

function initCalendar({
  datesTaches = [],
  titleId = "cal-title",
  gridId = "calendar-days",
  prevId = "cal-prev",
  nextId = "cal-next",
  dayCls = "cal-day", // 'cal-day' pour dashboard, 'agenda-day' pour agenda
  onDayClick = null,
}) {
  const MOIS = [
    "Janvier",
    "Février",
    "Mars",
    "Avril",
    "Mai",
    "Juin",
    "Juillet",
    "Août",
    "Septembre",
    "Octobre",
    "Novembre",
    "Décembre",
  ];

  const today = new Date();
  let mois = today.getMonth();
  let annee = today.getFullYear();

  const titre = document.getElementById(titleId);
  const grille = document.getElementById(gridId);
  const btnPrev = document.getElementById(prevId);
  const btnNext = document.getElementById(nextId);
  if (!titre || !grille || !btnPrev || !btnNext) return;

  function render() {
    const ym = `${annee}-${String(mois + 1).padStart(2, "0")}`;
    const nbJours = new Date(annee, mois + 1, 0).getDate();
    let debutSem = new Date(annee, mois, 1).getDay();
    debutSem = debutSem === 0 ? 7 : debutSem;

    titre.textContent = `📅 ${MOIS[mois]} ${annee}`;

    const joursAvecTaches = datesTaches
      .filter((d) => d?.startsWith(ym))
      .map((d) => parseInt(d.split("-")[2], 10));

    let html = "";
    for (let i = 1; i < debutSem; i++) {
      html += `<div class="${dayCls} empty"></div>`;
    }
    for (let j = 1; j <= nbJours; j++) {
      let cls = dayCls;
      if (
        annee === today.getFullYear() &&
        mois === today.getMonth() &&
        j === today.getDate()
      )
        cls += " today";
      if (joursAvecTaches.includes(j)) cls += " has-task";

      const click = onDayClick
        ? `onclick="_calClick(${j}, ${mois + 1}, ${annee})"`
        : "";

      // Pour agenda : afficher le numéro + compteur
      const count = joursAvecTaches.includes(j)
        ? datesTaches.filter((d) => d === `${ym}-${String(j).padStart(2, "0")}`)
            .length
        : 0;

      const inner =
        dayCls === "agenda-day"
          ? `<span class="agenda-day-num">${j}</span>${count > 0 ? `<span class="agenda-task-count">${count}</span>` : ""}`
          : j;

      html += `<div class="${cls}" ${click}>${inner}</div>`;
    }
    grille.innerHTML = html;
  }

  if (onDayClick) window._calClick = (j, m, a) => onDayClick(j, m, a);

  btnPrev.addEventListener("click", () => {
    mois--;
    if (mois < 0) {
      mois = 11;
      annee--;
    }
    render();
  });
  btnNext.addEventListener("click", () => {
    mois++;
    if (mois > 11) {
      mois = 0;
      annee++;
    }
    render();
  });

  render();
}
