let documentClickHandler = null;

function closeMenu(navMenu) {
    navMenu.classList.remove("show");
    document.body.classList.remove("menu-open");
}

function initNavbar() {
    const navButton = document.querySelector("#main-navbar .nav-button");
    const navMenu = document.querySelector("#main-navbar .nav-menu");

    if (!navButton || !navMenu) return;

    // Ferme le menu au chargement
    closeMenu(navMenu);

    // Supprime l'ancien listener sur document avant d'en créer un nouveau
    if (documentClickHandler) {
        document.removeEventListener("click", documentClickHandler);
    }

    // Clone pour supprimer les anciens listeners du bouton
    const newNavButton = navButton.cloneNode(true);
    navButton.parentNode.replaceChild(newNavButton, navButton);

    // Ouvre / ferme le menu
    newNavButton.addEventListener("click", function(event) {
        event.stopPropagation();
        navMenu.classList.toggle("show");
        document.body.classList.toggle("menu-open");
    });

    // Stocke le handler pour pouvoir le supprimer plus tard
    documentClickHandler = function() {
        closeMenu(navMenu);
    };
    document.addEventListener("click", documentClickHandler);

    // Ferme le menu au clic sur un lien
    navMenu.querySelectorAll("a").forEach(link => {
        link.addEventListener("click", function() {
            closeMenu(navMenu);
        });
    });
}

if (typeof Turbo !== "undefined") {
    document.addEventListener("turbo:load", initNavbar);
} else {
    window.addEventListener("load", initNavbar);
}