<?php
/* ==========================================================
   FOKUS — modals.php
   ========================================================== */

$notif_password = $_SESSION['notif_password'] ?? null;
$notif_delete   = $_SESSION['notif_delete']   ?? null;
unset($_SESSION['notif_password'], $_SESSION['notif_delete']);

// Génération du token CSRF (une seule fois par session)
require_once __DIR__ . '/security.php';
$csrf = generer_csrf_token();
?>

<!-- ══════════════════════════════
   MODAL — Ajouter une tâche
══════════════════════════════ -->
<div class="modal-overlay" id="modal-add-task" role="dialog" aria-modal="true" aria-labelledby="mat-title">
  <div class="modal">
    <div class="modal-header">
      <h2 class="modal-title" id="mat-title">✦ Nouvelle Tâche</h2>
      <button class="modal-close" onclick="closeModal('modal-add-task')" aria-label="Fermer">×</button>
    </div>
    <form action="../php/add/add_task.php" method="POST" novalidate>
      <input type="hidden" name="csrf_token" value="<?= $csrf ?>" />
      <div class="form-group">
        <label for="add-titre">Titre</label>
        <input type="text" id="add-titre" name="titre" class="form-control" placeholder="Nom de la tâche" required />
      </div>
      <div class="form-group">
        <label for="add-categorie">Catégorie</label>
        <select id="add-categorie" name="categorie_id" class="form-control">
          <option value="">Aucune</option>
          <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['ID_C'] ?>"><?= htmlspecialchars($cat['NOM_C']) ?></option>
            <?php endforeach; ?>
          <?php endif; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="add-date">Date limite <span style="font-weight:400;opacity:.6">(optionnel)</span></label>
        <input type="date" id="add-date" name="date_limite" class="form-control" />
      </div>
      <div class="form-group">
        <label>Statut</label>
        <div class="status-group">
          <label class="status-option">
            <input type="radio" name="statut" value="en_cours" checked />
            <span class="status-dot dot-inprog"></span>
            <span class="status-label">En cours</span>
          </label>
          <label class="status-option">
            <input type="radio" name="statut" value="a_faire" />
            <span class="status-dot dot-todo"></span>
            <span class="status-label">À faire</span>
          </label>
          <label class="status-option">
            <input type="radio" name="statut" value="termine" />
            <span class="status-dot dot-done"></span>
            <span class="status-label">Terminée</span>
          </label>
        </div>
      </div>
      <div class="form-group">
        <label for="add-description">Description</label>
        <textarea id="add-description" name="description" class="form-control" placeholder="Décrivez votre tâche..."></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-outline" onclick="closeModal('modal-add-task')">Annuler</button>
        <button type="submit" class="btn-gold">Valider</button>
      </div>
    </form>
  </div>
</div>

<!-- ══════════════════════════════
   MODAL — Modifier une tâche
══════════════════════════════ -->
<div class="modal-overlay" id="modal-edit-task" role="dialog" aria-modal="true" aria-labelledby="met-title">
  <div class="modal">
    <div class="modal-header">
      <h2 class="modal-title" id="met-title">✏️ Modifier la Tâche</h2>
      <button class="modal-close" onclick="closeModal('modal-edit-task')" aria-label="Fermer">×</button>
    </div>
    <form action="../php/edit/edit_task.php" method="POST" novalidate>
      <input type="hidden" name="csrf_token" value="<?= $csrf ?>" />
      <input type="hidden" name="task_id" id="edit-task-id" value="" />
      <div class="form-group">
        <label for="edit-titre">Titre</label>
        <input type="text" id="edit-titre" name="titre" class="form-control" placeholder="Nom de la tâche" required />
      </div>
      <div class="form-group">
        <label for="edit-categorie">Catégorie</label>
        <select id="edit-categorie" name="categorie_id" class="form-control">
          <option value="">Aucune</option>
          <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['ID_C'] ?>"><?= htmlspecialchars($cat['NOM_C']) ?></option>
            <?php endforeach; ?>
          <?php endif; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="edit-date">Date limite <span style="font-weight:400;opacity:.6">(optionnel)</span></label>
        <input type="date" id="edit-date" name="date_limite" class="form-control" />
      </div>
      <div class="form-group">
        <label>Statut</label>
        <div class="status-group">
          <label class="status-option">
            <input type="radio" name="statut" value="en_cours" />
            <span class="status-dot dot-inprog"></span>
            <span class="status-label">En cours</span>
          </label>
          <label class="status-option">
            <input type="radio" name="statut" value="a_faire" />
            <span class="status-dot dot-todo"></span>
            <span class="status-label">À faire</span>
          </label>
          <label class="status-option">
            <input type="radio" name="statut" value="termine" />
            <span class="status-dot dot-done"></span>
            <span class="status-label">Terminée</span>
          </label>
        </div>
      </div>
      <div class="form-group">
        <label for="edit-description">Description</label>
        <textarea id="edit-description" name="description" class="form-control" placeholder="Décrivez votre tâche..."></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-outline" onclick="closeModal('modal-edit-task')">Annuler</button>
        <button type="submit" class="btn-gold">Appliquer</button>
      </div>
    </form>
  </div>
</div>

<!-- ══════════════════════════════
   MODAL — Ajouter une catégorie
══════════════════════════════ -->
<div class="modal-overlay" id="modal-add-categorie" role="dialog" aria-modal="true" aria-labelledby="mac-title">
  <div class="modal" style="max-width: 420px">
    <div class="modal-header">
      <h2 class="modal-title" id="mac-title">🗂️ Nouvelle Catégorie</h2>
      <button class="modal-close" onclick="closeModal('modal-add-categorie')" aria-label="Fermer">×</button>
    </div>
    <form action="../php/add/add_categorie.php" method="POST" novalidate>
      <input type="hidden" name="csrf_token" value="<?= $csrf ?>" />
      <div class="form-group">
        <label for="cat-nom">Nom de la catégorie</label>
        <input type="text" id="cat-nom" name="nom" class="form-control" placeholder="Ex : Études, Dev, Perso..." required maxlength="50" />
      </div>
      <div class="form-group">
        <label for="cat-desc">Description <span style="font-weight:400;opacity:.6">(optionnel)</span></label>
        <input type="text" id="cat-desc" name="description" class="form-control" placeholder="Description courte..." maxlength="100" />
      </div>
      <div class="form-group">
        <label>Couleur</label>
        <input type="color" name="color" value="#c9a227" />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-outline" onclick="closeModal('modal-add-categorie')">Annuler</button>
        <button type="submit" class="btn-gold">Créer</button>
      </div>
    </form>
  </div>
</div>

<!-- ══════════════════════════════
   MODAL — Modifier une catégorie
══════════════════════════════ -->
<div class="modal-overlay" id="modal-edit-categorie" role="dialog" aria-modal="true" aria-labelledby="mec-title">
  <div class="modal" style="max-width: 420px">
    <div class="modal-header">
      <h2 class="modal-title" id="mec-title">✏️ Modifier la catégorie</h2>
      <button class="modal-close" onclick="closeModal('modal-edit-categorie')">×</button>
    </div>
    <form action="../php/edit/edit_categorie.php" method="POST" novalidate>
      <input type="hidden" name="csrf_token" value="<?= $csrf ?>" />
      <input type="hidden" name="cat_id" id="edit-cat-id" value="" />
      <div class="form-group">
        <label>Nom</label>
        <input type="text" name="nom" id="edit-cat-nom" class="form-control" placeholder="Nom de la catégorie" required />
      </div>
      <div class="form-group">
        <label>Description</label>
        <input type="text" name="description" id="edit-cat-desc" class="form-control" placeholder="Description..." />
      </div>
      <div class="form-group">
        <label>Couleur</label>
        <input type="color" name="color" id="edit-cat-color" value="#c9a227" />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-outline" onclick="closeModal('modal-edit-categorie')">Annuler</button>
        <button type="submit" class="btn-gold">Sauvegarder</button>
      </div>
    </form>
  </div>
</div>

<!-- ══════════════════════════════
   MODAL — Ajouter un objectif
══════════════════════════════ -->
<div class="modal-overlay" id="modal-add-objectif" role="dialog" aria-modal="true" aria-labelledby="ao-title">
  <div class="modal" style="max-width: 400px">
    <div class="modal-header">
      <h2 class="modal-title" id="ao-title">🎯 Nouvel Objectif</h2>
      <button class="modal-close" onclick="closeModal('modal-add-objectif')" aria-label="Fermer">×</button>
    </div>
    <form action="../php/add/add_objectif.php" method="POST" novalidate>
      <input type="hidden" name="csrf_token" value="<?= $csrf ?>" />
      <div class="form-group">
        <label for="obj-text">Objectif</label>
        <input type="text" id="obj-text" name="objectif" class="form-control" placeholder="Décrivez votre objectif..." required />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-outline" onclick="closeModal('modal-add-objectif')">Annuler</button>
        <button type="submit" class="btn-gold">Ajouter</button>
      </div>
    </form>
  </div>
</div>

<!-- ══════════════════════════════
   MODAL — Modifier un objectif
══════════════════════════════ -->
<div class="modal-overlay" id="modal-edit-objectif" role="dialog" aria-modal="true" aria-labelledby="eo-title">
  <div class="modal" style="max-width: 400px">
    <div class="modal-header">
      <h2 class="modal-title" id="eo-title">✏️ Modifier l'objectif</h2>
      <button class="modal-close" onclick="closeModal('modal-edit-objectif')" aria-label="Fermer">×</button>
    </div>
    <form action="../php/edit/edit_goal.php" method="POST" novalidate>
      <input type="hidden" name="csrf_token" value="<?= $csrf ?>" />
      <input type="hidden" name="ancien_titre" id="edit-goal-ancien" value="" />
      <input type="hidden" name="nom" id="edit-obj-id" value="" />
      <div class="form-group">
        <label for="edit-goal-nouveau">Objectif</label>
        <input type="text" id="edit-goal-nouveau" name="nouveau_titre" class="form-control" placeholder="Nouvel objectif..." required />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-outline" onclick="closeModal('modal-edit-objectif')">Annuler</button>
        <button type="submit" class="btn-gold">Sauvegarder</button>
      </div>
    </form>
  </div>
</div>

<!-- ══════════════════════════════
   MODAL — Modifier le profil
══════════════════════════════ -->
<div class="modal-overlay" id="modal-modify-profil" role="dialog" aria-modal="true" aria-labelledby="mmp-title">
  <div class="modal">
    <div class="modal-header">
      <h2 class="modal-title" id="mmp-title">✏️ Modifier mes informations</h2>
      <button class="modal-close" onclick="closeModal('modal-modify-profil')" aria-label="Fermer">×</button>
    </div>
    <form action="../php/edit/update_profil.php" method="POST" novalidate>
      <input type="hidden" name="csrf_token" value="<?= $csrf ?>" />
      <div class="form-row">



        <div class="form-group">
          <label for="edit-username">Nom d'utilisateur</label>
          <input type="text" id="edit-username" name="username" class="form-control" placeholder="Laisser vide pour ne pas changer" />
        </div>
        <div class="form-group">
          <label for="edit-email">Adresse e-mail</label>
          <input type="email" id="edit-email" name="email" class="form-control" placeholder="Laisser vide pour ne pas changer" />
        </div>
      </div>
      <div class="form-group">
        <label for="edit-nb-tache">Nombre de tâches par page</label>
        <input type="text" id="edit-nb-tache" name="nb_tache" class="form-control" placeholder="Laisser vide pour ne pas changer" min="1" max="20" />
      </div>
      <div class="form-group">
        <label for="edit-curr-password-profil">Mot de passe actuel <span style="color:var(--status-urgent)">*</span></label>
        <div class="password-wrapper">
          <input type="password" id="edit-curr-password-profil" name="curr_password" class="form-control" required />
          <button type="button" class="toggle-password" aria-label="Afficher">👁</button>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-outline" onclick="closeModal('modal-modify-profil')">Annuler</button>
        <button type="submit" class="btn-gold">Sauvegarder</button>
      </div>
    </form>
  </div>
</div>

<!-- ══════════════════════════════
   MODAL — Photo de profil
══════════════════════════════ -->

<div class="modal-overlay" id="modal-photo-profil" role="dialog" aria-modal="true" aria-labelledby="mpp-title">
  <div class="modal" style="max-width: 360px; text-align: center;">
    <div class="modal-header">
      <h2 class="modal-title" id="mpp-title">📷 Photo de profil</h2>
      <button class="modal-close" onclick="closeModal('modal-photo-profil')" aria-label="Fermer">×</button>
    </div>

    <div style="padding: 24px 20px; display:flex; flex-direction:column; align-items:center; gap:20px;">

      <!-- Aperçu de la photo -->
      <?php $photo_profil = is_string($photo_profil ?? null) ? $photo_profil : null;
      $first_letter = $first_letter ?? '?';?>
      <?php if ($photo_profil): ?>
    <img src="<?= htmlspecialchars(is_string($photo_profil) ? $photo_profil : '') ?>"
             alt="Photo de profil"
             class="avatar-img avatar-img-lg" />
      <?php else: ?>
        <div class="avatar-placeholder avatar-placeholder-lg">
          <?= htmlspecialchars($first_letter) ?>
        </div>
      <?php endif; ?>

      <!-- Bouton modifier -->
      <form action="/php/add/upload_photo.php"
            method="POST"
            enctype="multipart/form-data"
            style="width:100%">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generer_csrf_token()) ?>" />
        <input type="file"
               id="photo-input"
               name="photo"
               accept="image/jpeg,image/png,image/webp,image/gif"
               style="display:none"
               onchange="this.form.submit()" />
        <button type="button" class="btn-gold btn-full"
                onclick="document.getElementById('photo-input').click()">
          📷 Modifier la photo
        </button>
      </form>

      <!-- Bouton supprimer (uniquement si photo existe) -->
      <?php if ($photo_profil): ?>
        <form action="/php/delete/delete_photo.php"
              method="POST"
              style="width:100%">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generer_csrf_token()) ?>" />
          <button type="submit" class="btn-outline btn-full"
                  style="color:var(--status-urgent); border-color:var(--status-urgent);">
            🗑 Supprimer la photo
          </button>
        </form>
      <?php endif; ?>

    </div>
  </div>
</div>

<!-- ══════════════════════════════
   MODAL — Modifier le mot de passe
══════════════════════════════ -->
<div class="modal-overlay" id="modal-modify-password" role="dialog" aria-modal="true" aria-labelledby="mmdp-title">
  <div class="modal">
    <div class="modal-header">
      <h2 class="modal-title" id="mmdp-title">🔒 Modifier le mot de passe</h2>
      <button class="modal-close" onclick="closeModal('modal-modify-password')" aria-label="Fermer">×</button>
    </div>
    <form action="../php/edit/update_password.php" method="POST" novalidate>
      <input type="hidden" name="csrf_token" value="<?= $csrf ?>" />
      <div class="form-group">
        <label for="curr-password-mdp">Mot de passe actuel</label>
        <div class="password-wrapper">
          <input type="password" id="curr-password-mdp" name="curr_password" class="form-control" required />
          <button type="button" class="toggle-password" aria-label="Afficher">👁</button>
        </div>
      </div>
      <div class="form-group">
        <label for="reg-password">Nouveau mot de passe</label>
        <div class="password-wrapper">
          <input type="password" id="reg-password" name="new_password" class="form-control" placeholder="Nouveau mot de passe" autocomplete="new-password" required />
          <button type="button" class="toggle-password" aria-label="Afficher">👁</button>
        </div>
        <div class="strength-bar">
          <div class="strength-fill" id="strength-fill"></div>
        </div>
        <span class="strength-label" id="strength-label"></span>
      </div>
      <div class="form-group">
        <label for="confirm-password-mdp">Confirmer le mot de passe</label>
        <div class="password-wrapper">
          <input type="password" id="confirm-password-mdp" name="confirm_password" class="form-control" placeholder="Répéter le nouveau mot de passe" autocomplete="new-password" required />
          <button type="button" class="toggle-password" aria-label="Afficher">👁</button>
        </div>
      </div>

      <hr style="border-color: var(--border); margin: 8px 0;" />

      <div class="form-group">
        <label for="edit-question">Question secrète <span style="font-weight:400;opacity:.6">(optionnel)</span></label>
        <select id="edit-question" name="question" class="form-control">
          <option value="">— Choisir une question —</option>
          <option value="1">Quel est le prénom de votre mère ?</option>
          <option value="2">Quel est le nom de votre premier animal ?</option>
          <option value="3">Quelle est votre ville de naissance ?</option>
          <option value="4">Quel est le titre de votre livre préféré ?</option>
          <option value="5">Quel est le prénom de votre meilleur(e) ami(e) d'enfance ?</option>
        </select>
      </div>
      <div class="form-group">
        <label for="edit-reponse">Réponse secrète <span style="font-weight:400;opacity:.6">(optionnel)</span></label>
        <input type="text" id="edit-reponse" name="reponse" class="form-control" placeholder="Votre réponse..." />
      </div>

      <div class="modal-footer">
        <button type="button" class="btn-outline" onclick="closeModal('modal-modify-password')">Annuler</button>
        <button type="submit" class="btn-gold">Sauvegarder</button>
      </div>
    </form>
  </div>
</div>

<!-- ══════════════════════════════
   MODAL — Voir le détail d'une tâche
══════════════════════════════ -->
<div class="modal-overlay" id="modal-view-task" role="dialog" aria-modal="true" aria-labelledby="mvt-title">
  <div class="modal modal-view">
    <div class="modal-header">
      <h2 class="modal-title" id="mvt-title">
        <span id="view-urgent-badge" style="display:none; color: var(--status-urgent); margin-right:6px;">🚨 URGENT</span>
        <span id="view-nom"></span>
      </h2>
      <button class="modal-close" onclick="closeModal('modal-view-task')" aria-label="Fermer">×</button>
    </div>
    <div class="view-task-body">
      <div class="view-task-meta">
        <div class="view-task-meta-item">
          <span class="view-meta-label">Statut</span>
          <span id="view-statut" class="badge"></span>
        </div>
        <div class="view-task-meta-item" id="view-cat-wrap">
          <span class="view-meta-label">Catégorie</span>
          <span id="view-cat" class="task-item-cat"></span>
        </div>
        <div class="view-task-meta-item" id="view-date-wrap">
          <span class="view-meta-label">Date limite</span>
          <span id="view-date" class="task-item-date"></span>
        </div>
      </div>
      <div class="view-task-desc-block" id="view-desc-wrap">
        <span class="view-meta-label">Description</span>
        <p id="view-desc" class="view-task-desc"></p>
      </div>
      <div id="view-no-desc" style="display:none;">
        <span style="color: var(--text-muted); font-style: italic; font-size: 0.85rem;">Aucune description</span>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn-outline" onclick="closeModal('modal-view-task')">Fermer</button>
      <button type="button" class="btn-gold" id="view-edit-btn">✏️ Modifier</button>
    </div>
  </div>
</div>

<!-- ══════════════════════════════
   MODAL — Erreur
══════════════════════════════ -->
<div class="modal-overlay" id="modal-error">
  <div class="modal" style="max-width: 380px; text-align: center;">
    <div class="modal-header" style="justify-content: center; border: none;">
      <span style="font-size: 2rem;">⚠️</span>
    </div>
    <p id="modal-error-msg" style="color: var(--text-secondary); margin-bottom: 24px; font-size: 0.95rem;"></p>
    <button class="btn-gold btn-full" onclick="closeModal('modal-error')">Fermer</button>
  </div>
</div>

<!-- ══════════════════════════════
   MODAL — Succès
══════════════════════════════ -->
<div class="modal-overlay" id="modal-sucess">
  <div class="modal" style="max-width: 380px; text-align: center;">
    <div class="modal-header" style="justify-content: center; border: none;">
      <span style="font-size: 2rem;">✅</span>
    </div>
    <p id="modal-sucess-msg" style="color: var(--text-secondary); margin-bottom: 24px; font-size: 0.95rem;"></p>
    <button class="btn-gold btn-full" onclick="closeModal('modal-sucess')">Fermer</button>
  </div>
</div>

<!-- ══════════════════════════════
   MODAL — Confirmation suppression
══════════════════════════════ -->
<div class="modal-overlay" id="modal-confirm">
  <div class="modal" style="max-width: 400px; text-align: center;">
    <div class="modal-header" style="justify-content:center; border:none;">
      <span style="font-size: 2rem;">⚠️</span>
    </div>
    <p id="modal-confirm-msg" style="margin: 20px 0; color: var(--text-secondary);"></p>
    <div style="display:flex; gap:10px;">
      <button class="btn-gold btn-full" id="confirm-yes">Oui</button>
      <button class="btn-secondary btn-full" onclick="closeModal('modal-confirm')">Annuler</button>
    </div>
  </div>
</div>

<!-- ══════════════════════════════
   MODAL — Notifications
══════════════════════════════ -->
<div class="modal-overlay" id="modal-notification" role="dialog" aria-modal="true" aria-labelledby="mn-title">
  <div class="modal" style="max-width: 460px">
    <div class="modal-header">
      <h2 class="modal-title" id="mn-title">🔔 Notifications</h2>
      <button class="modal-close" onclick="closeModal('modal-notification')" aria-label="Fermer">×</button>
    </div>
    <div class="notif-list">

      <?php if (!empty($taches_en_retard)): ?>
        <div class="notif-section-title">🔴 En retard</div>
        <?php foreach ($taches_en_retard as $t): ?>
          <div class="notif-item">
            <div class="notif-icon">🔴</div>
            <div>
              <div class="notif-text"><strong>"<?= htmlspecialchars($t['NOM_T']) ?>"</strong> est en retard</div>
              <div class="notif-time">📅 <?= htmlspecialchars($t['LIMIT_DATE']) ?></div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>

      <?php if (!empty($taches_urgentes_actives)): ?>
        <div class="notif-section-title">⚡ Urgentes</div>
        <?php foreach ($taches_urgentes_actives as $t): ?>
          <div class="notif-item">
            <div class="notif-icon">⚡</div>
            <div>
              <div class="notif-text"><strong>"<?= htmlspecialchars($t['NOM_T']) ?>"</strong> est marquée urgente</div>
              <div class="notif-time"><?= $t['LIMIT_DATE'] ? '📅 ' . htmlspecialchars($t['LIMIT_DATE']) : 'Pas de date limite' ?></div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>

      <?php if (!empty($taches_aujourdhui)): ?>
        <div class="notif-section-title">📅 Aujourd'hui</div>
        <?php foreach ($taches_aujourdhui as $t): ?>
          <div class="notif-item">
            <div class="notif-icon">📅</div>
            <div>
              <div class="notif-text"><strong>"<?= htmlspecialchars($t['NOM_T']) ?>"</strong> est à rendre aujourd'hui</div>
              <div class="notif-time">Échéance ce jour</div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>

      <?php if ($notif_password || $notif_delete): ?>
        <div class="notif-section-title">🔐 Sécurité & Actions</div>
        <?php if ($notif_password): ?>
          <div class="notif-item">
            <div class="notif-icon">🔐</div>
            <div>
              <div class="notif-text">Votre <strong>mot de passe</strong> a été modifié</div>
              <div class="notif-time">Aujourd'hui à <?= htmlspecialchars($notif_password) ?></div>
            </div>
          </div>
        <?php endif; ?>
        <?php if ($notif_delete): ?>
          <div class="notif-item">
            <div class="notif-icon">🗑️</div>
            <div>
              <div class="notif-text">Des <strong>tâches</strong> ont été supprimées en masse</div>
              <div class="notif-time">Aujourd'hui à <?= htmlspecialchars($notif_delete) ?></div>
            </div>
          </div>
        <?php endif; ?>
      <?php endif; ?>

      <?php if (empty($taches_en_retard) && empty($taches_urgentes_actives) && empty($taches_aujourdhui) && !$notif_password && !$notif_delete): ?>
        <div class="notif-item">
          <div class="notif-icon">✅</div>
          <div>
            <div class="notif-text">Tout est à jour, aucune alerte !</div>
          </div>
        </div>
      <?php endif; ?>

    </div>
  </div>
</div>


<!-- ══════════════════════════════
   MODAL — AJOUT DE PHOTO DE PROFIL
══════════════════════════════ -->

<div class="modal-overlay" id="modal-photo-profil" role="dialog" aria-modal="true" aria-labelledby="mpp-title">
  <div class="modal" style="max-width: 380px; text-align: center;">
    <div class="modal-header">
      <h2 class="modal-title" id="mpp-title">📷 Photo de profil</h2>
      <button class="modal-close" onclick="closeModal('modal-photo-profil')" aria-label="Fermer">×</button>
    </div>

    <div style="padding: 24px 20px;">

      <form action="/php/edit/upload_photo.php"
            method="POST"
            enctype="multipart/form-data">

        <input type="hidden" name="csrf_token"
               value="<?= htmlspecialchars(generer_csrf_token()) ?>" />

        <label for="photo-input" class="avatar-upload-label" style="display:inline-block; margin-bottom: 20px;">
          <?php if ($photo_profil): ?>
            <img src="<?= htmlspecialchars($photo_profil) ?>"
                 alt="Photo de profil"
                 class="avatar-img avatar-img-lg" />
          <?php else: ?>
            <div class="avatar-placeholder avatar-placeholder-lg">
              <?= htmlspecialchars($first_letter) ?>
            </div>
          <?php endif; ?>
          <span class="avatar-upload-overlay">📷 Changer</span>
        </label>

        <input type="file"
               id="photo-input"
               name="photo"
               accept="image/jpeg,image/png,image/webp,image/gif"
               style="display:none"
               onchange="this.form.submit()" />

        <p style="font-size:0.8rem; color:var(--text-muted); margin-bottom: 20px;">
          Formats acceptés : JPG, PNG, WEBP, GIF — max 2 Mo
        </p>

        <div class="modal-footer" style="justify-content: center;">
          <button type="button" class="btn-outline" onclick="closeModal('modal-photo-profil')">Fermer</button>
          <button type="button" class="btn-gold" onclick="document.getElementById('photo-input').click()">
            📷 Choisir une photo
          </button>
        </div>

      </form>

    </div>
  </div>
</div>