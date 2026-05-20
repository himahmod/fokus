"use strict";

const errorMessages = {
  /* Champs & validation */
  champs_vides: "Veuillez remplir tous les champs obligatoires.",
  champ_trop_long: "Un champ dépasse la longueur maximale autorisée.",
  couleur_invalide: "La couleur saisie est invalide.",
  date_invalide: "La date saisie est invalide.",
  cgu: "Conditions générales d'utilisations non acceptées.",
  nb_taches_invalide: "Nombre de tâches selectionées invalide.",

  /* Email */
  email_invalide: "Adresse e-mail invalide.",
  email_pris: "Cet email est déjà utilisé.",

  /* Mot de passe */
  mdp_court: "Le mot de passe doit faire au moins 8 caractères.",
  mdp_trop_long: "Le mot de passe est trop long (maximum 72 caractères).",
  mdp_faible:
    "Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre.",
  mdp_different: "Les mots de passe ne correspondent pas.",
  mdp_faux: "Le mot de passe actuel est incorrect.",
  mdp_oblg: "Le mot de passe est obligatoire.",

  /* Authentification */
  identifiants_incorrects: "Email ou mot de passe incorrect.",
  mauvaise_réponse: "Réponse secrète incorrecte.",

  /* Sécurité */
  csrf_invalide: "Session expirée, veuillez recharger la page.",
  methode_invalide: "Requête invalide.",
  erreur_serveur: "Une erreur serveur est survenue, veuillez réessayer.",

  /* Utilisateur */
  username_pris: "Ce nom d'utilisateur est déjà pris.",
  utilisateur_introuvable: "Utilisateur introuvable.",

  /*photo*/
  photo_trop_lourde: "La photo ne doit pas dépasser 2 Mo.",
  photo_type_invalide: "Format invalide. Utilisez JPG, PNG, WEBP ou GIF.",
};

const params = new URLSearchParams(window.location.search);
const errorKey = params.get("error");

if (errorKey && errorMessages[errorKey]) {
  document.getElementById("modal-error-msg").textContent =
    errorMessages[errorKey];
  openModal("modal-error");
  history.replaceState(null, "", window.location.pathname);
}
