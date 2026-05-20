/* ==========================================================
   FOKUS — main.js
   Interactions UI : menu, modals, onglets, filtres, recherche
   ========================================================== */

/* ----------------------------------------------------------
   MENU MOBILE — toggle sidebar
---------------------------------------------------------- */
"use strict";

const sidebar = document.getElementById("sidebar");
const menuToggle = document.getElementById("menu-toggle");
const sidebarBackdrop = document.getElementById("sidebar-backdrop");

if (menuToggle && sidebar) {
  menuToggle.addEventListener("click", () => {
    sidebar.classList.toggle("open");
    sidebarBackdrop && sidebarBackdrop.classList.toggle("active");
  });
}
if (sidebarBackdrop) {
  sidebarBackdrop.addEventListener("click", () => {
    sidebar && sidebar.classList.remove("open");
    sidebarBackdrop.classList.remove("active");
  });
}

/* ----------------------------------------------------------
   MODALS — open / close
---------------------------------------------------------- */
function openModal(id) {
  const overlay = document.getElementById(id);
  if (!overlay) return;
  overlay.classList.add("active");
  document.body.style.overflow = "hidden";

  /* Focus sur le premier champ */
  const firstInput = overlay.querySelector("input, select, textarea");
  if (firstInput) setTimeout(() => firstInput.focus(), 50);
}

function closeModal(id) {
  const overlay = document.getElementById(id);
  if (!overlay) return;
  overlay.classList.remove("active");
  document.body.style.overflow = "";
}

/* Fermer en cliquant sur l'overlay (hors du .modal) */
document.querySelectorAll(".modal-overlay").forEach((overlay) => {
  overlay.addEventListener("click", (e) => {
    if (e.target === overlay) closeModal(overlay.id);
  });
});

/* Fermer avec Escape */
document.addEventListener("keydown", (e) => {
  if (e.key === "Escape") {
    document
      .querySelectorAll(".modal-overlay.active")
      .forEach((o) => closeModal(o.id));
  }
});

/* ----------------------------------------------------------
   PAGE LOGIN — onglets Connexion / Inscription
---------------------------------------------------------- */
const authTabs = document.querySelectorAll(".auth-tab");
const authForms = document.querySelectorAll(".auth-form");

authTabs.forEach((tab) => {
  tab.addEventListener("click", () => {
    const target = tab.dataset.tab;

    authTabs.forEach((t) => {
      t.classList.remove("active");
      t.setAttribute("aria-selected", "false");
    });
    authForms.forEach((f) => f.classList.remove("active"));

    tab.classList.add("active");
    tab.setAttribute("aria-selected", "true");
    const form = document.getElementById(`form-${target}`);
    if (form) form.classList.add("active");
  });
});

/* ----------------------------------------------------------
   TOGGLE MOT DE PASSE — afficher / masquer
---------------------------------------------------------- */
document.querySelectorAll(".toggle-password").forEach((btn) => {
  btn.addEventListener("click", () => {
    const wrapper = btn.closest(".password-wrapper");
    const input = wrapper && wrapper.querySelector("input");
    if (!input) return;
    const isHidden = input.type === "password";
    input.type = isHidden ? "text" : "password";
    btn.textContent = isHidden ? "🙈" : "👁";
  });
});

/* ----------------------------------------------------------
   PAGE MES TÂCHES — filtres par statut
---------------------------------------------------------- */
const filterTabs = document.querySelectorAll(".filter-tab");

filterTabs.forEach((tab) => {
  tab.addEventListener("click", () => {
    filterTabs.forEach((t) => t.classList.remove("active"));
    tab.classList.add("active");
    filterTasks(tab.dataset.filter);
  });
});

function filterTasks(filter) {
  const items = document.querySelectorAll(".task-item");
  items.forEach((item) => {
    const col = item.closest(".kanban-col");
    const colType = col ? col.dataset.col : "";
    const show =
      filter === "all" ||
      filter === colType ||
      (filter === "urgent" && item.classList.contains("urgent"));

    item.style.display = show ? "" : "none";
  });
}

/* ----------------------------------------------------------
   PAGE MES TÂCHES — recherche en temps réel
---------------------------------------------------------- */
const searchInput = document.getElementById("search-tasks");

if (searchInput) {
  searchInput.addEventListener("input", () => {
    const q = searchInput.value.trim().toLowerCase();
    document.querySelectorAll(".task-item").forEach((item) => {
      const title = item.querySelector(".task-item-title");
      const text = (title ? title.textContent : "").toLowerCase();
      item.style.display = !q || text.includes(q) ? "" : "none";
    });
  });
}

/* ----------------------------------------------------------
   PAGINATION — boutons page actifs
---------------------------------------------------------- */
document.querySelectorAll(".pagination-btns, .pagination").forEach((nav) => {
  nav.querySelectorAll(".page-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      const isArrow =
        btn.textContent.includes("›") || btn.textContent.includes("‹");
      if (isArrow) return;
      nav
        .querySelectorAll(".page-btn")
        .forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");
    });
  });
});

/* ----------------------------------------------------------
   NOTIFICATIONS — panneau (structure de base)
---------------------------------------------------------- */
const notifBtn = document.getElementById("notif-btn");
if (notifBtn) {
  notifBtn.addEventListener("click", () => {
    /* À développer : afficher un dropdown de notifications */
    console.log("Notifications cliquées — à implémenter côté PHP");
  });
}

/* ----------------------------------------------------------
   ACTIVER LE LIEN SIDEBAR selon la page courante
---------------------------------------------------------- */
(function highlightNav() {
  const path = window.location.pathname;
  const links = document.querySelectorAll(".nav-item");
  links.forEach((link) => {
    if (link.href && path.endsWith(link.getAttribute("href"))) {
      links.forEach((l) => l.classList.remove("active"));
      link.classList.add("active");
    }
  });
})();

/* ==========================================================
   À AJOUTER à la fin de main.js
   ========================================================== */

/* ----------------------------------------------------------
   DASHBOARD — pagination par colonne
   Les tâches sont déjà groupées en .task-page côté PHP.
   On affiche une seule .task-page à la fois (.active)
   et on bascule avec les flèches ‹ / ›.
---------------------------------------------------------- */
(function dashboardColumnPagination() {
  const columns = document.querySelectorAll(".task-column[data-column]");
  if (!columns.length) return;

  columns.forEach((col) => {
    const list = col.querySelector(".task-list");
    if (!list) return;

    const pages = list.querySelectorAll(".task-page");
    const pager = col.querySelector(".task-col-pager");
    if (!pager) return;

    const totalPages = pages.length;
    const btnPrev = pager.querySelector('[data-dir="prev"]');
    const btnNext = pager.querySelector('[data-dir="next"]');
    const elCurrent = pager.querySelector(".page-current");
    const elTotal = pager.querySelector(".page-total");

    /* Pas de pagination si 0 ou 1 seule page → on cache le pager */
    if (totalPages <= 1) {
      pager.style.display = "none";
      return;
    }

    let currentPage = 1;

    function render() {
      pages.forEach((p) => {
        const num = parseInt(p.dataset.page, 10);
        p.classList.toggle("active", num === currentPage);
      });
      elCurrent.textContent = currentPage;
      elTotal.textContent = totalPages;
      btnPrev.disabled = currentPage === 1;
      btnNext.disabled = currentPage === totalPages;
    }

    btnPrev.addEventListener("click", () => {
      if (currentPage > 1) {
        currentPage--;
        render();
      }
    });

    btnNext.addEventListener("click", () => {
      if (currentPage < totalPages) {
        currentPage++;
        render();
      }
    });

    render();
  });
})();
