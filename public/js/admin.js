// public/js/admin.js

document.addEventListener("DOMContentLoaded", () => {
    const buttons = document.querySelectorAll(".tab-btn");
    const contentContainer = document.getElementById("admin-content");

    // Je gère le clic sur les onglets principaux du panneau admin
    buttons.forEach(button => {
        button.addEventListener("click", () => {
            const tab = button.dataset.tab; // Je récupère l’identifiant de l’onglet cliqué

            // Je mets à jour visuellement l’onglet actif
            buttons.forEach(btn => btn.classList.remove("active"));
            button.classList.add("active");

            // Je charge dynamiquement le contenu de l’onglet via fetch, avec un header AJAX
            fetch(`${BASE_URL}/admin/${tab}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Erreur lors du chargement du contenu.");
                    }
                    return response.text();
                })
                .then(html => {
                    // J’injecte le contenu HTML dans la zone principale
                    contentContainer.innerHTML = html;

                    // Je relance les fonctions nécessaires après chargement
                    initSubtabs();                     // Pour réactiver les sous-onglets (films/BD)
                    autoDismissMessages();             // Pour faire disparaître les messages
                    activerConfirmationSuppression();  // Pour gérer la modale de suppression AJAX
                    activerConfirmationDesactivation(); // Pour gérer la modale de désactivation AJAX
                })
                .catch(error => {
                    // En cas d’erreur, j’affiche un message visible dans le container
                    contentContainer.innerHTML = `<p style="color:red;">Erreur : ${error.message}</p>`;
                });
        });
    });

    // Je gère les sous-onglets présents dans certains onglets (par exemple : films / BD)
    function initSubtabs() {
        const sousOnglets = document.querySelectorAll(".subtab-btn");
        const contenus = document.querySelectorAll(".subtab-content");

        if (sousOnglets.length && contenus.length) {
            sousOnglets.forEach(btn => {
                btn.addEventListener("click", () => {
                    const cible = btn.dataset.subtab;

                    // Je mémorise le dernier sous-onglet actif dans le localStorage
                    localStorage.setItem("ongletOeuvreActif", cible);

                    // Je mets à jour l’état actif visuellement
                    sousOnglets.forEach(b => b.classList.remove("active"));
                    btn.classList.add("active");

                    // Je masque/affiche dynamiquement les blocs de contenu
                    contenus.forEach(div => {
                        div.style.display = div.id === cible ? "block" : "none";
                    });
                });
            });

            // Je relis le dernier sous-onglet consulté, ou je prends "films" par défaut
            const ongletSauvegarde = localStorage.getItem("ongletOeuvreActif");
            const boutonCible = document.querySelector(`.subtab-btn[data-subtab="${ongletSauvegarde}"]`);
            const boutonDefaut = document.querySelector('.subtab-btn[data-subtab="films"]');

            if (boutonCible) {
                boutonCible.click();
            } else if (boutonDefaut) {
                boutonDefaut.click();
            }
        }
    }

    // Je déclenche une première activation des sous-onglets au chargement initial
    initSubtabs();

    // Je fais disparaître automatiquement les messages de confirmation ou d’erreur
    function autoDismissMessages() {
        setTimeout(() => {
            const messages = document.querySelectorAll('.message-success, .message-error');
            messages.forEach(msg => {
                msg.style.transition = "opacity 0.5s ease";
                msg.style.opacity = "0";
                setTimeout(() => msg.remove(), 500); // Je supprime le message après le fondu
            });
        }, 5000);
    }

    // Je déclenche la suppression auto au démarrage
    autoDismissMessages();
});
