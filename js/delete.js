"use strict";

/* ----------------------------------------------------------
   CONFIRM MESSAGES
---------------------------------------------------------- */
const confirms = {
  delete_task: "Supprimer cette tâche ?",
  delete_all_tasks: "Supprimer toutes les tâches ?",
  delete_category: "Supprimer cette catégorie ?",
  delete_all_categories: "Supprimer toutes les catégories ?",
  delete_goal: "Supprimer cet objectif ?",
  delete_all_goals: "Supprimer tous vos objectifs ?",
  delete_account: "Supprimer votre compte ? Cette action est irréversible.",
  delete_taskscolumn: "Supprimer toutes les tâches de cette colonne ?",
};

let confirmAction = null;

/* ----------------------------------------------------------
   OPEN MODAL CONFIRM
---------------------------------------------------------- */
function openConfirm(type, callback) {
  const msg = confirms[type];

  if (!msg) return;

  document.getElementById("modal-confirm-msg").textContent = msg;
  confirmAction = callback;

  openModal("modal-confirm");
}

/* ----------------------------------------------------------
   VALIDATION BOUTON "OUI"
---------------------------------------------------------- */
document.addEventListener("DOMContentLoaded", () => {
  const yesBtn = document.getElementById("confirm-yes");

  yesBtn.addEventListener("click", () => {
    closeModal("modal-confirm");

    if (typeof confirmAction === "function") {
      confirmAction();
    }
  });
});

/* ----------------------------------------------------------
   SUPPRESSION TÂCHE
---------------------------------------------------------- */
function SuppressionTache(id) {
  openConfirm("delete_task", () => {
    location.href = "/php/delete/delete_task.php?id=" + id;
  });
}

/* ----------------------------------------------------------
   SUPPRESSION TOUTES TÂCHES
---------------------------------------------------------- */
function SuppressionTacheAll() {
  openConfirm("delete_all_tasks", () => {
    location.href = "/php/delete/delete_alltasks.php";
  });
}
/* ----------------------------------------------------------
   SUPPRESSION TOUTES TÂCHES DE COLONNE
---------------------------------------------------------- */
function SuppressionTachesCol(statut) {
  openConfirm("delete_taskscolumn", () => {
    location.href = "/php/delete/delete_taskscolumn.php?statut=" + statut;
  });
}

/* ----------------------------------------------------------
   SUPPRESSION CATÉGORIE
---------------------------------------------------------- */
function SuppressionCategorie(id) {
  openConfirm("delete_category", () => {
    location.href = "/php/delete/delete_categorie.php?id=" + id;
  });
}

/* ----------------------------------------------------------
   SUPPRESSION TOUTES CATÉGORIES
---------------------------------------------------------- */
function SuppressionCategorieAll() {
  openConfirm("delete_all_categories", () => {
    location.href = "/php/delete/delete_allcategories.php";
  });
}

/* ----------------------------------------------------------
   SUPPRESSION OBJECTIF
---------------------------------------------------------- */
function SuppressionObjectif(titre) {
  openConfirm("delete_goal", () => {
    location.href =
      "/php/delete/delete_goal.php?titre=" + encodeURIComponent(titre);
  });
}

/* ----------------------------------------------------------
   SUPPRESSION TOUS OBJECTIFS
---------------------------------------------------------- */
function SuppressionObjectifAll() {
  openConfirm("delete_all_goals", () => {
    location.href = "/php/delete/delete_allgoals.php";
  });
}

/* ----------------------------------------------------------
   SUPPRESSION COMPTE 
---------------------------------------------------------- */
function SuppressionCompte() {
  openConfirm("delete_account", () => {
    location.href = "/php/delete/delete_account.php";
  });
}
