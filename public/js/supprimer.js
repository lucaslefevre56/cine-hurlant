// public/js/supprimer.js

// Fonction pour activer la confirmation avant toute suppression (via lien ou formulaire)
function activerConfirmationSuppression() {
    const modal = document.getElementById("supprimer-confirm-modal");    // Modale de confirmation
    const confirmBtn = document.getElementById("confirm-supprimer");     // Bouton de confirmation
    const cancelBtn = document.getElementById("cancel-supprimer");       // Bouton d’annulation

    if (!modal || !confirmBtn || !cancelBtn) return; // Si l’un des éléments est manquant, on quitte

    let actionCible = null;  // Élément ciblé par l’action (form ou lien)
    let typeAction = null;   // Type d’action : "form" ou "link"

    // Cas des suppressions via lien
    document.querySelectorAll(".btn-supprimer[href]").forEach(lien => {
        lien.addEventListener("click", function (e) {
            e.preventDefault();             // On empêche l’action par défaut
            actionCible = lien;             // On enregistre l’élément
            typeAction = "link";
            modal.style.display = "flex";   // On affiche la modale
        });
    });

    // Cas des suppressions via formulaire
    document.querySelectorAll("form:has(.btn-supprimer)").forEach(form => {
        // Empêche d’ajouter plusieurs fois l’écouteur
        if (!form.dataset.confirmationActive) {
            form.addEventListener("submit", function (e) {
                e.preventDefault();         // Empêche l’envoi direct
                actionCible = form;
                typeAction = "form";
                modal.style.display = "flex";
            });
            form.dataset.confirmationActive = "true";
        }
    });

    // Si l’utilisateur confirme la suppression
    confirmBtn.addEventListener("click", () => {
        if (actionCible) {
            if (typeAction === "link") {
                window.location.href = actionCible.href;
            } else if (typeAction === "form") {
                actionCible.submit();
            }
        }
        modal.style.display = "none";
        actionCible = null;
    });

    // Si l’utilisateur annule
    cancelBtn.addEventListener("click", () => {
        modal.style.display = "none";
        actionCible = null;
    });
}

// Fonction pour activer la confirmation avant désactivation (via formulaire uniquement)
function activerConfirmationDesactivation() {
    const modal = document.getElementById("desactivation-confirm-modal");  // Modale de désactivation
    const confirmBtn = document.getElementById("confirm-desactivation");
    const cancelBtn = document.getElementById("cancel-desactivation");

    if (!modal || !confirmBtn || !cancelBtn) return;

    let formTarget = null; // Formulaire ciblé

    // Recherche des formulaires contenant un bouton de désactivation
    document.querySelectorAll("form:has(.btn-desactiver)").forEach(form => {
        // Nettoyage si l’événement existe déjà (rechargement AJAX)
        form.removeEventListener?.("submit", form._desactiverHandler);

        const handler = function (e) {
            e.preventDefault();         // Empêche l’envoi immédiat
            formTarget = form;
            modal.style.display = "flex";
        };

        form.addEventListener("submit", handler);
        form._desactiverHandler = handler; // Je stocke la référence pour pouvoir le retirer si besoin
    });

    // Si l’utilisateur confirme, j’envoie le formulaire
    confirmBtn.addEventListener("click", () => {
        if (formTarget) formTarget.submit();
        modal.style.display = "none";
        formTarget = null;
    });

    // Si l’utilisateur annule
    cancelBtn.addEventListener("click", () => {
        modal.style.display = "none";
        formTarget = null;
    });
}

// Fonction globale appelée au chargement ou après un chargement AJAX
function initialiserConfirmationsModales() {
    activerConfirmationSuppression();
    activerConfirmationDesactivation();
}

// Initialisation automatique au chargement complet du DOM
document.addEventListener("DOMContentLoaded", () => {
    initialiserConfirmationsModales();
});
