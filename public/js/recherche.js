// public/js/recherche.js

// Je m’assure que le DOM est bien chargé avant d’agir
document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("#form-recherche");                      // Le formulaire de recherche
    const input = form.querySelector("input[name='q']");                         // Le champ texte de recherche
    const select = form.querySelector("select[name='type']");                    // Le menu déroulant pour le type (article / œuvre / tous)
    const resultatsDiv = document.querySelector("#resultats-recherche");         // Le bloc d’affichage des résultats

    // Je vérifie que tous les éléments attendus sont présents avant d’aller plus loin
    if (!form || !input || !select || !resultatsDiv) return;

    let timer; // Je crée un timer pour gérer le debounce (éviter de surcharger l’API à chaque touche)

    // ----- Recherche en direct (keyup) avec un petit délai ----- //
    input.addEventListener("keyup", () => {
        const query = input.value.trim(); // Je récupère et nettoie le mot-clé
        const type = select.value;        // Je récupère le filtre sélectionné

        clearTimeout(timer); // J’annule tout timer précédent

        // Je ne lance pas la recherche si moins de 2 caractères
        if (query.length < 2) {
            resultatsDiv.style.display = "none";
            return;
        }

        // J’attends 300ms avant d’envoyer la requête (évite le spam à chaque frappe)
        timer = setTimeout(() => {
            lancerRecherche(query, type);
        }, 300);
    });

    // ----- Soumission classique (clic sur Entrée ou bouton) ----- //
    form.addEventListener("submit", (e) => {
        const query = input.value.trim();
        if (query.length < 2) {
            e.preventDefault(); // Je bloque si la requête est trop courte
        }
    });

    // ----- Clique à l’extérieur du formulaire ou du bloc de résultats → je masque ----- //
    document.addEventListener("click", (e) => {
        if (!form.contains(e.target) && !resultatsDiv.contains(e.target)) {
            resultatsDiv.style.display = "none";
        }
    });

    // ----- Fonction principale qui envoie la requête AJAX et affiche les résultats ----- //
    async function lancerRecherche(query, type) {
        try {
            const url = `${BASE_URL}/public/api/recherche.php?q=${encodeURIComponent(query)}&type=${encodeURIComponent(type)}`;
            const response = await fetch(url);
            const data = await response.json();

            resultatsDiv.style.display = "block"; // Je rends le bloc visible

            // Si l’API renvoie une erreur, je l’affiche et je stoppe
            if (data.error) {
                resultatsDiv.innerHTML = `<p class="erreur">${escapeHtml(data.error)}</p>`;
                return;
            }

            // Je commence à construire dynamiquement le bloc HTML
            let html = `
                <section class="bloc-suggestions">
                    <h3>Résultats pour : <span>${escapeHtml(data.query)}</span></h3>
            `;

            // Si des œuvres sont trouvées, je les affiche
            if (data.oeuvres.length > 0) {
                html += `
                    <div class="bloc-resultat">
                        <h4>Œuvres</h4>
                        <ul>`;
                data.oeuvres.forEach(o => {
                    html += `<li><a href="${BASE_URL}/oeuvre/fiche/${o.id_oeuvre}">${escapeHtml(o.titre)}</a> (${escapeHtml(o.type)})</li>`;
                });
                html += `</ul>
                    </div>`;
            }

            // Si des articles sont trouvés, je les affiche également
            if (data.articles.length > 0) {
                html += `
                    <div class="bloc-resultat">
                        <h4>Articles</h4>
                        <ul>`;
                data.articles.forEach(a => {
                    html += `<li><a href="${BASE_URL}/article/fiche/${a.id_article}">${escapeHtml(a.titre)}</a> par ${escapeHtml(a.auteur)}</li>`;
                });
                html += `</ul>
                    </div>`;
            }

            // Si rien n’a été trouvé, je le signale
            if (data.oeuvres.length === 0 && data.articles.length === 0) {
                html += `<p>Aucun résultat trouvé.</p>`;
            }

            // Je termine avec un bouton pour masquer les résultats
            html += `
                    <div style="text-align:right; margin-top: 0.5rem;">
                        <button id="fermer-resultats"
                            style="background:none;border:none;color:#999;font-size:1.2rem;cursor:pointer;"
                            aria-label="Fermer les résultats">✖️</button>
                    </div>
                </section>
            `;

            resultatsDiv.innerHTML = html;

            // Fermeture manuelle via le bouton ✖️
            const btnFermer = document.getElementById("fermer-resultats");
            if (btnFermer) {
                btnFermer.addEventListener("click", () => {
                    resultatsDiv.style.display = "none";
                });
            }

        } catch (error) {
            console.error("Erreur AJAX recherche :", error);
            resultatsDiv.innerHTML = `<p class="erreur">Une erreur est survenue.</p>`;
            resultatsDiv.style.display = "block";
        }
    }

    // Fonction de protection XSS pour échapper les caractères HTML
    function escapeHtml(text) {
        const div = document.createElement("div");
        div.textContent = text;
        return div.innerHTML;
    }
});
