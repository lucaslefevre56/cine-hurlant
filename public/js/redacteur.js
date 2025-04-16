// public/js/redacteur.js

document.addEventListener("DOMContentLoaded", () => {
    const boutons = document.querySelectorAll(".tab-btn");
    const conteneur = document.getElementById("redacteur-content");

    // 1. Gestion des clics sur les onglets (articles / oeuvres)
    boutons.forEach(bouton => {
        bouton.addEventListener("click", () => {
            const onglet = bouton.dataset.tab;

            // Affichage visuel de l'onglet actif
            boutons.forEach(btn => btn.classList.remove("active"));
            bouton.classList.add("active");

            // Requête AJAX pour charger le contenu
            fetch(`${BASE_URL}/redacteur/${onglet}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    if (!response.ok) throw new Error("Erreur lors du chargement de la vue.");
                    return response.text();
                })
                .then(html => {
                    conteneur.innerHTML = html;
                    initSubtabs();               // Réactive les sous-onglets
                    autoDismissMessages();       // Réactive la disparition des messages
                    activerConfirmationSuppression(); // 🆕 Confirmation suppression
                })
                .catch(error => {
                    conteneur.innerHTML = `<p style="color:red;">${error.message}</p>`;
                });
        });
    });

    // 2. Activation des sous-onglets (films / BD) si présents
    function initSubtabs() {
        const sousOnglets = document.querySelectorAll(".subtab-btn");
        const contenus = document.querySelectorAll(".subtab-content");

        if (sousOnglets.length && contenus.length) {
            sousOnglets.forEach(btn => {
                btn.addEventListener("click", () => {
                    const cible = btn.dataset.subtab;

                    localStorage.setItem("redacteurOngletActif", cible);

                    sousOnglets.forEach(b => b.classList.remove("active"));
                    btn.classList.add("active");

                    contenus.forEach(div => {
                        div.style.display = div.id === cible ? "block" : "none";
                    });
                });
            });

            const actif = localStorage.getItem("redacteurOngletActif");
            const boutonActif = document.querySelector(`.subtab-btn[data-subtab="${actif}"]`);
            const boutonDefaut = document.querySelector('.subtab-btn[data-subtab="films"]');

            if (boutonActif) {
                boutonActif.click();
            } else if (boutonDefaut) {
                boutonDefaut.click();
            }
        }
    }

    // 3. Suppression automatique des messages après 5 secondes
    function autoDismissMessages() {
        setTimeout(() => {
            const messages = document.querySelectorAll('.message-success, .message-error');
            messages.forEach(msg => {
                msg.style.transition = "opacity 0.5s ease";
                msg.style.opacity = "0";
                setTimeout(() => msg.remove(), 500);
            });
        }, 5000);
    }

    // Initialisation immédiate au chargement
    initSubtabs();
    autoDismissMessages();
    activerConfirmationSuppression(); // pour les suppressions visibles dès le chargement
});
