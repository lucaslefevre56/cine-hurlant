// public/js/redacteur.js

document.addEventListener("DOMContentLoaded", () => {
    const boutons = document.querySelectorAll(".tab-btn");
    const conteneur = document.getElementById("redacteur-content");

    // Je gère les clics sur les onglets principaux (Articles / Œuvres)
    boutons.forEach(bouton => {
        bouton.addEventListener("click", () => {
            const onglet = bouton.dataset.tab;

            // Mise à jour de l'onglet actif visuellement
            boutons.forEach(btn => btn.classList.remove("active"));
            bouton.classList.add("active");

            // Chargement dynamique du contenu correspondant via fetch()
            fetch(`${BASE_URL}/redacteur/${onglet}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest' // Permet au contrôleur de détecter l'appel AJAX
                }
            })
                .then(response => {
                    if (!response.ok) throw new Error("Erreur lors du chargement de la vue.");
                    return response.text(); // Je récupère le HTML en texte
                })
                .then(html => {
                    conteneur.innerHTML = html;         // Injection du contenu
                    initSubtabs();                      // Réactivation des sous-onglets (films/BD)
                    autoDismissMessages();              // Disparition automatique des messages flash
                    activerConfirmationSuppression();   // Activation des modales de confirmation de suppression
                })
                .catch(error => {
                    conteneur.innerHTML = `<p style="color:red;">${error.message}</p>`;
                });
        });
    });

    // Active les sous-onglets (films / BD) s’ils sont présents dans le contenu chargé
    function initSubtabs() {
        const sousOnglets = document.querySelectorAll(".subtab-btn");
        const contenus = document.querySelectorAll(".subtab-content");

        if (sousOnglets.length && contenus.length) {
            sousOnglets.forEach(btn => {
                btn.addEventListener("click", () => {
                    const cible = btn.dataset.subtab;

                    // Je mémorise le dernier onglet actif pour le restaurer plus tard
                    localStorage.setItem("redacteurOngletActif", cible);

                    // Mise à jour des boutons
                    sousOnglets.forEach(b => b.classList.remove("active"));
                    btn.classList.add("active");

                    // Affichage du bon contenu
                    contenus.forEach(div => {
                        div.style.display = div.id === cible ? "block" : "none";
                    });
                });
            });

            // Je restaure l’onglet précédemment actif (films par défaut)
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

    // Permet de faire disparaître automatiquement les messages flash (succès ou erreur)
    function autoDismissMessages() {
        setTimeout(() => {
            const messages = document.querySelectorAll('.message-success, .message-error');
            messages.forEach(msg => {
                msg.style.transition = "opacity 0.5s ease";
                msg.style.opacity = "0";
                setTimeout(() => msg.remove(), 500); // Je supprime le message après le fondu
            });
        }, 5000); // Attente initiale avant le fondu
    }

    // Appels directs des fonctions après le premier chargement complet
    initSubtabs();
    autoDismissMessages();
    activerConfirmationSuppression(); // Je m’assure que les boutons déjà présents soient bien actifs
});
