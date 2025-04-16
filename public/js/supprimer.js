function activerConfirmationSuppression() {
    const modal = document.getElementById("supprimer-confirm-modal");
    const confirmBtn = document.getElementById("confirm-supprimer");
    const cancelBtn = document.getElementById("cancel-supprimer");

    if (!modal || !confirmBtn || !cancelBtn) return;

    let actionCible = null;
    let typeAction = null;

    document.querySelectorAll(".btn-supprimer[href]").forEach(lien => {
        lien.addEventListener("click", function (e) {
            e.preventDefault();
            actionCible = lien;
            typeAction = "link";
            modal.style.display = "flex";
        });
    });

    document.querySelectorAll("form:has(.btn-supprimer)").forEach(form => {
        if (!form.dataset.confirmationActive) {
            form.addEventListener("submit", function (e) {
                e.preventDefault();
                actionCible = form;
                typeAction = "form";
                modal.style.display = "flex";
            });
            form.dataset.confirmationActive = "true";
        }
    });

    confirmBtn.addEventListener("click", () => {
        if (actionCible) {
            if (typeAction === "link") window.location.href = actionCible.href;
            else if (typeAction === "form") actionCible.submit();
        }
        modal.style.display = "none";
        actionCible = null;
    });

    cancelBtn.addEventListener("click", () => {
        modal.style.display = "none";
        actionCible = null;
    });
}

function activerConfirmationDesactivation() {
    const modal = document.getElementById("desactivation-confirm-modal");
    const confirmBtn = document.getElementById("confirm-desactivation");
    const cancelBtn = document.getElementById("cancel-desactivation");

    if (!modal || !confirmBtn || !cancelBtn) return;

    let formTarget = null;

    document.querySelectorAll("form:has(.btn-desactiver)").forEach(form => {
        form.removeEventListener?.("submit", form._desactiverHandler); // nettoie l'ancien si rechargement AJAX

        const handler = function (e) {
            e.preventDefault();
            formTarget = form;
            modal.style.display = "flex";
        };

        form.addEventListener("submit", handler);
        form._desactiverHandler = handler;
    });

    confirmBtn.addEventListener("click", () => {
        if (formTarget) formTarget.submit();
        modal.style.display = "none";
        formTarget = null;
    });

    cancelBtn.addEventListener("click", () => {
        modal.style.display = "none";
        formTarget = null;
    });
}

// Fonction relançable après AJAX
function initialiserConfirmationsModales() {
    activerConfirmationSuppression();
    activerConfirmationDesactivation();
}

// 📦 Lancement global
document.addEventListener("DOMContentLoaded", () => {
    initialiserConfirmationsModales();
});
