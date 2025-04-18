// public/js/annuler.js

// J’attends que le DOM soit entièrement chargé avant d’activer la fonctionnalité
document.addEventListener("DOMContentLoaded", () => {
    activerConfirmationAnnulation(); // J’active la gestion des boutons "Annuler" avec modale
});

// Fonction qui active la confirmation d’annulation via une modale personnalisée
function activerConfirmationAnnulation() {
    const liensAnnuler = document.querySelectorAll(".btn-annuler");             // Tous les liens qui doivent déclencher la modale
    const modal = document.getElementById("annuler-confirm-modal");             // La fenêtre modale à afficher
    const confirmBtn = document.getElementById("confirm-annuler");              // Le bouton "Confirmer" dans la modale
    const cancelBtn = document.getElementById("cancel-annuler");                // Le bouton "Annuler" dans la modale

    // Je m’assure que la modale et les boutons existent dans la page
    if (!modal || !confirmBtn || !cancelBtn) return;

    let lienCible = null; // Je stocke temporairement le lien sur lequel on a cliqué

    // Pour chaque lien "Annuler", j’ajoute un comportement au clic
    liensAnnuler.forEach(lien => {
        lien.addEventListener("click", function (e) {
            e.preventDefault();         // Je bloque la redirection immédiate
            lienCible = lien;           // Je garde une référence vers le lien concerné
            modal.style.display = "flex"; // J’affiche la modale
        });
    });

    // Si l’utilisateur confirme l’action, je redirige vers le lien ciblé
    confirmBtn.addEventListener("click", () => {
        if (lienCible) {
            window.location.href = lienCible.href;
        }
        modal.style.display = "none"; // Je masque la modale après validation
    });

    // Si l’utilisateur annule l’action, je ferme la modale sans rien faire
    cancelBtn.addEventListener("click", () => {
        modal.style.display = "none";
        lienCible = null; // Je réinitialise la cible
    });
}
