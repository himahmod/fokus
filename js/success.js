"use strict";

const successMessages = {
  task_added: "Tâche ajoutée avec succès.",
  task_edited: "Tâche modifiée avec succès.",
  task_deleted: "Tâche supprimée avec succès.",
  tasks_deleted: "Toutes les tâches ont été supprimées.",
  tasks_deleted_col: "Toutes les tâches de la colonne ont été supprimées.",
  cat_added: "Catégorie ajoutée avec succès.",
  cat_edited: "Catégorie modifiée avec succès.",
  cat_deleted: "Catégorie supprimée avec succès.",
  cats_deleted: "Toutes les catégories ont été supprimées.",
  goal_added: "Objectif ajouté avec succès.",
  goal_edited: "Objectif modifié avec succès.",
  goal_deleted: "Objectif supprimé avec succès.",
  goals_deleted: "Tous les objectifs ont été supprimés.",
  profil_updated: "Profil mis à jour avec succès.",
  urgent_updated: "Tâche mise à jour avec succès.",
  mdp_modifie: "Informations modifiées avec succès.",
  compte_cree: "Votre compte a été créé avec succès.",
  compte_supprime: "Votre compte a été supprimé avec succès.",
  photo_updated: "Photo de profil mise à jour avec succès.",
  photo_deleted: "Photo de profil supprimée.",
};

const successParams = new URLSearchParams(window.location.search);
const successKey = successParams.get("success");

if (successKey && successMessages[successKey]) {
  document.getElementById("modal-sucess-msg").textContent =
    successMessages[successKey];
  openModal("modal-sucess");
  history.replaceState(null, "", window.location.pathname);
}
