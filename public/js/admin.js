document.addEventListener("DOMContentLoaded", () => {
    const buttons = document.querySelectorAll(".tab-btn");
    const contentContainer = document.getElementById("admin-content");

    // 1. Gestion du clic utilisateur sur les onglets principaux (Utilisateurs, Articles, etc.)
    buttons.forEach(button => {
        button.addEventListener("click", () => {
            const tab = button.dataset.tab;

            // Visuel actif
            buttons.forEach(btn => btn.classList.remove("active"));
            button.classList.add("active");

            // Chargement dynamique du contenu de l’onglet
            fetch(`${BASE_URL}/admin/${tab}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Erreur lors du chargement du contenu.");
                    }
                    return response.text();
                })
                .then(html => {
                    contentContainer.innerHTML = html;
                    initSubtabs(); // Active les sous-onglets si présents
                })
                .catch(error => {
                    contentContainer.innerHTML = `<p style="color:red;">Erreur : ${error.message}</p>`;
                });
        });
    });

    // 2. Si un onglet est défini via PHP → clic automatique
    if (typeof ongletParSession !== "undefined" && ongletParSession) {
        const bouton = document.querySelector(`[data-tab="${ongletParSession}"]`);
        if (bouton) bouton.click();
    }

    // 3. Activation des sous-onglets (films / bd) si présents dans la vue des œuvres
    function initSubtabs() {
        const sousOnglets = document.querySelectorAll(".subtab-btn");
        const contenus = document.querySelectorAll(".subtab-content");

        if (sousOnglets.length && contenus.length) {
            sousOnglets.forEach(btn => {
                btn.addEventListener("click", () => {
                    const cible = btn.dataset.subtab;

                    sousOnglets.forEach(b => b.classList.remove("active"));
                    btn.classList.add("active");

                    contenus.forEach(div => {
                        div.style.display = div.id === cible ? "block" : "none";
                    });
                });
            });

            // Onglet par défaut (films visible)
            const boutonDefaut = document.querySelector('.subtab-btn[data-subtab="films"]');
            if (boutonDefaut) boutonDefaut.click();
        }
    }
});
