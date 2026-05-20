"use strict";
/* ----------------------------------------------------------
    Modifier une categorie
---------------------------------------------------------- */
function ouvrirEditCategorie(id, nom, desc, color) {
  document.getElementById("edit-cat-id").value = id;
  document.getElementById("edit-cat-nom").value = nom;
  document.getElementById("edit-cat-desc").value = desc;
  document.getElementById("edit-cat-color").value = color;
  openModal("modal-edit-categorie");
}

/* ----------------------------------------------------------
    Modifier une objectif
---------------------------------------------------------- */
function ouvrirEditObjectif(titre) {
  document.getElementById("edit-goal-ancien").value = titre;
  document.getElementById("edit-goal-nouveau").value = titre;
  openModal("modal-edit-objectif");
}

/* ----------------------------------------------------------
    Modifier une Tache
---------------------------------------------------------- */
function ouvrirEditTache(
  id,
  titre,
  categorieId,
  dateLimite,
  statut,
  description,
) {
  document.getElementById("edit-task-id").value = id;
  document.getElementById("edit-titre").value = titre;
  document.getElementById("edit-categorie").value = categorieId ?? "";
  document.getElementById("edit-date").value = dateLimite ?? "";
  document.getElementById("edit-description").value = description ?? "";

  // Cocher le bon statut
  const radios = document.querySelectorAll('input[name="statut"]');
  radios.forEach((r) => (r.checked = r.value === statut));

  openModal("modal-edit-task");
}

function toggleUrgent(btn, id) {
  fetch("../php/edit/urgent.php?id=" + id).then((response) => {
    if (response.ok) {
      location.reload();
    }
  });
}
/* ==========================================================
   À AJOUTER dans main.js
   (ou dans un fichier view.js séparé chargé dans les pages)
   ========================================================== */

/* ----------------------------------------------------------
   MODAL VUE DÉTAIL — tâche
   Appelé par onclick="ouvrirViewTache(this)" sur .task-item
---------------------------------------------------------- */
function ouvrirViewTache(el) {
  const d = el.dataset;

  /* Nom */
  document.getElementById("view-nom").textContent = d.nom;

  /* Badge urgent */
  const urgentBadge = document.getElementById("view-urgent-badge");
  urgentBadge.style.display = d.urgent === "1" ? "inline" : "none";

  /* Statut */
  const statutEl = document.getElementById("view-statut");
  const statutLabels = {
    en_cours: { label: "En cours", cls: "badge-inprog" },
    a_faire: { label: "À faire", cls: "badge-todo" },
    termine: { label: "Terminée", cls: "badge-done" },
  };
  const s = statutLabels[d.statut] || { label: d.statut, cls: "" };
  statutEl.textContent = s.label;
  statutEl.className = "badge " + s.cls;

  /* Catégorie */
  const catWrap = document.getElementById("view-cat-wrap");
  const catEl = document.getElementById("view-cat");
  if (d.cat) {
    catEl.textContent = d.cat;
    catEl.style.backgroundColor = d.catColor || "";
    catWrap.style.display = "";
  } else {
    catWrap.style.display = "none";
  }

  /* Date */
  const dateWrap = document.getElementById("view-date-wrap");
  const dateEl = document.getElementById("view-date");
  if (d.date) {
    dateEl.textContent = "📅 " + d.date;
    dateWrap.style.display = "";
  } else {
    dateWrap.style.display = "none";
  }

  /* Description */
  const descWrap = document.getElementById("view-desc-wrap");
  const noDescWrap = document.getElementById("view-no-desc");
  const descEl = document.getElementById("view-desc");
  if (d.desc && d.desc.trim()) {
    descEl.textContent = d.desc;
    descWrap.style.display = "";
    noDescWrap.style.display = "none";
  } else {
    descWrap.style.display = "none";
    noDescWrap.style.display = "";
  }

  /* Bouton Modifier — relie au modal edit */
  const editBtn = document.getElementById("view-edit-btn");
  editBtn.onclick = () => {
    closeModal("modal-view-task");
    /* Retrouver les valeurs JSON encodées depuis l'élément pour ouvrirEditTache */
    const btns = el.querySelectorAll(".task-action-btn");
    /* Le bouton ✏️ est le 2e bouton dans task-item-actions */
    const editAction = el.querySelector('[title="Modifier"]');
    if (editAction) editAction.click();
  };

  openModal("modal-view-task");
}
