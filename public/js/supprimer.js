function activerConfirmationSuppression() {
    const modal = document.getElementById("supprimer-confirm-modal");
    const confirmBtn = document.getElementById("confirm-supprimer");
    const cancelBtn = document.getElementById("cancel-supprimer");

    if (!modal || !confirmBtn || !cancelBtn) return;

    let actionCible = null;
    let typeAction = null; // 'link' ou 'form'

    // ✅ Gestion des liens de suppression (admin)
    const liens = document.querySelectorAll(".btn-supprimer[href]");
    liens.forEach(lien => {
        lien.addEventListener("click", function (e) {
            e.preventDefault();
            actionCible = lien;
            typeAction = "link";
            modal.style.display = "flex";
        });
    });

    // ✅ Gestion des formulaires de suppression (rédacteur)
    const formulaires = document.querySelectorAll("form:has(.btn-supprimer)");
    formulaires.forEach(form => {
        form.addEventListener("submit", function (e) {
            e.preventDefault();
            actionCible = form;
            typeAction = "form";
            modal.style.display = "flex";
        });
    });

    // 🔴 Bouton "Oui, supprimer"
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

    // 🔵 Bouton "Annuler"
    cancelBtn.addEventListener("click", () => {
        modal.style.display = "none";
        actionCible = null;
    });
}
