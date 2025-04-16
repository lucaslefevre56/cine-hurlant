document.addEventListener("DOMContentLoaded", () => {
    const buttons = document.querySelectorAll(".tab-btn");
    const contentContainer = document.getElementById("admin-content");

    // 1. Gestion du clic utilisateur sur les onglets principaux
    buttons.forEach(button => {
        button.addEventListener("click", () => {
            const tab = button.dataset.tab;

            // Mise à jour de l'onglet actif visuellement
            buttons.forEach(btn => btn.classList.remove("active"));
            button.classList.add("active");

            // Chargement dynamique du contenu de l’onglet via fetch() avec header AJAX
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
                    contentContainer.innerHTML = html;
                    initSubtabs();            // Réactive les sous-onglets
                    autoDismissMessages();    // Réactive les messages
                    activerConfirmationSuppression(); // 🔥 Confirmation suppression AJAX
                })
                .catch(error => {
                    contentContainer.innerHTML = `<p style="color:red;">Erreur : ${error.message}</p>`;
                });
        });
    });

    // 2. Activer les sous-onglets (films / BD)
    function initSubtabs() {
        const sousOnglets = document.querySelectorAll(".subtab-btn");
        const contenus = document.querySelectorAll(".subtab-content");

        if (sousOnglets.length && contenus.length) {
            sousOnglets.forEach(btn => {
                btn.addEventListener("click", () => {
                    const cible = btn.dataset.subtab;
                    localStorage.setItem("ongletOeuvreActif", cible);

                    sousOnglets.forEach(b => b.classList.remove("active"));
                    btn.classList.add("active");

                    contenus.forEach(div => {
                        div.style.display = div.id === cible ? "block" : "none";
                    });
                });
            });

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

    // 3. Activation automatique des sous-onglets après chargement complet
    initSubtabs();

    // 4. Suppression auto des messages après 5 secondes (avec fondu)
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

    autoDismissMessages();
});
