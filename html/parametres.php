<?php require_once __DIR__ . '/../php/session/data_user.php'; ?>
<?php require_once __DIR__ . '/../php/session/statistiques.php'; ?>
<!doctype html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FOKUS — Paramètres</title>
    <link rel="stylesheet" href="/css/global.css" />
    <link rel="stylesheet" href="/css/parametres.css" />
    <link rel="icon" type="image/png" href="/image/logo.png" />
    <?php include __DIR__ . '/../php/modals.php'; ?>
  </head>
  <body>

    <?php require_once __DIR__ . '/util_html_elem/nav_bar.php'; ?>
    <?php require_once __DIR__ . '/util_html_elem/header.php'; ?>

      <main class="app-main">
        <h1 class="page-title">Paramètres</h1>
        <p class="page-subtitle">Gérez votre compte et vos préférences</p>

        <div class="params-grid">

          <div class="params-left">

            <div class="params-card">
              <div class="params-card-header">
                <span class="params-card-icon">👤</span>
                <div>
                  <div class="params-card-title">Informations du compte</div>
                  <div class="params-card-sub">Modifiez vos informations personnelles</div>
                </div>
              </div>
              <div class="params-card-body">
                <div class="params-info-row">
                  <span class="params-info-label">Nom d'utilisateur</span>
                  <span class="params-info-value"><?= htmlspecialchars($username) ?></span>
                </div>
                <div class="params-info-row">
                  <span class="params-info-label">Adresse e-mail</span>
                  <span class="params-info-value"><?= htmlspecialchars($mail) ?></span>
                </div>
                <div class="params-info-row">
                  <span class="params-info-label">Rôle</span>
                  <span class="params-info-value"><?= htmlspecialchars($role_p) ?></span>
                </div>
                <div class="params-info-row">
                  <span class="params-info-label">Membre depuis</span>
                  <span class="params-info-value"><?= htmlspecialchars($signin_date) ?></span>
                </div>
                <div class="params-info-row">
                  <span class="params-info-label">Dernière connexion</span>
                  <span class="params-info-value"><?= htmlspecialchars($last_cnx) ?></span>
                </div>
                <div class="params-info-row">
                  <span class="params-info-label">Nombre de tache par page</span>
                  <span class="params-info-value"><?= htmlspecialchars($nb_taches_par_page) ?></span>
                </div>
              </div>
              <div class="params-card-footer">
                <button class="btn-gold" onclick="openModal('modal-modify-profil')">✏️ Modifier mes informations</button>
              </div>
            </div>

            <div class="params-card">
              <div class="params-card-header">
                <span class="params-card-icon">🔑</span>
                <div>
                  <div class="params-card-title">Sécurité</div>
                  <div class="params-card-sub">Gérez votre mot de passe et votre question secrète</div>
                </div>
              </div>
              <div class="params-card-body">
                <div class="params-info-row">
                  <span class="params-info-label">Mot de passe</span>
                  <span class="params-info-value">••••••••</span>
                </div>
                <div class="params-info-row">
                  <span class="params-info-label">Question secrète</span>
                  <span class="params-info-value">Configurée ✅</span>
                </div>
              </div>
              <div class="params-card-footer">
                <button class="btn-outline" onclick="openModal('modal-modify-password')">🔒 Changer le mot de passe</button>
              </div>
            </div>

          </div>

          <div class="params-right">

            <div class="params-card">
              <div class="params-card-header">
                <span class="params-card-icon">📊</span>
                <div>
                  <div class="params-card-title">Résumé de votre activité</div>
                  <div class="params-card-sub">Vue d'ensemble de vos données</div>
                </div>
              </div>
              <div class="params-stats-grid">
                <div class="params-stat">
                  <span class="params-stat-value"><?= $nb_taches ?></span>
                  <span class="params-stat-label">Tâches totales</span>
                </div>
                <div class="params-stat">
                  <span class="params-stat-value"><?= $nb_categories ?></span>
                  <span class="params-stat-label">Catégories</span>
                </div>
                <div class="params-stat done">
                  <span class="params-stat-value"><?= $nb_taches_termines ?></span>
                  <span class="params-stat-label">Terminées</span>
                </div>
                <div class="params-stat urgent">
                  <span class="params-stat-value"><?= $nb_taches_urgent ?></span>
                  <span class="params-stat-label">Urgentes</span>
                </div>
              </div>
            </div>

            <div class="params-card danger-card">
              <div class="params-card-header">
                <span class="params-card-icon">⚠️</span>
                <div>
                  <div class="params-card-title" style="color:var(--status-urgent)">Réinitialisation</div>
                  <div class="params-card-sub">Ces actions sont irréversibles</div>
                </div>
              </div>
              <div class="params-card-body">
                <div class="danger-row">
                  <div class="danger-text">
                    <div class="danger-title">Réinitialiser toutes mes tâches</div>
                    <div class="danger-desc">Supprime définitivement toutes vos tâches</div>
                  </div>
                  <button class="btn-danger" onclick="SuppressionTacheAll()">🗑 Supprimer</button>
                </div>
                <div class="danger-row">
                  <div class="danger-text">
                    <div class="danger-title">Supprimer toutes mes catégories</div>
                    <div class="danger-desc">Supprime définitivement toutes vos catégories</div>
                  </div>
                  <button class="btn-danger" onclick="SuppressionCategorieAll()">🗑 Supprimer</button>
                </div>
                <div class="danger-row">
                  <div class="danger-text">
                    <div class="danger-title">Supprimer mon compte</div>
                    <div class="danger-desc">Supprime votre compte et toutes vos données</div>
                  </div>
                  <button class="btn-danger" onclick="SuppressionCompte()">🗑 Supprimer</button>
                </div>
              </div>
            </div>

          </div>
        </div>
      </main>
    </div><!-- /.app-layout -->

    <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

    <script src="/js/main.js" defer></script>
    <script src="/js/delete.js" defer></script>
    <script src="/js/errors.js" defer></script>
    <script src="/js/success.js" defer></script>
  </body>
</html>