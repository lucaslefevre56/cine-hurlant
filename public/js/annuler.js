document.addEventListener("DOMContentLoaded", () => {
    activerConfirmationAnnulation();
});

function activerConfirmationAnnulation() {
    const liensAnnuler = document.querySelectorAll(".btn-annuler");
    const modal = document.getElementById("annuler-confirm-modal");
    const confirmBtn = document.getElementById("confirm-annuler");
    const cancelBtn = document.getElementById("cancel-annuler");

    if (!modal || !confirmBtn || !cancelBtn) return;

    let lienCible = null;

    liensAnnuler.forEach(lien => {
        lien.addEventListener("click", function (e) {
            e.preventDefault();
            lienCible = lien;
            modal.style.display = "flex";
        });
    });

    confirmBtn.addEventListener("click", () => {
        if (lienCible) {
            window.location.href = lienCible.href;
        }
        modal.style.display = "none";
    });

    cancelBtn.addEventListener("click", () => {
        modal.style.display = "none";
        lienCible = null;
    });
}
