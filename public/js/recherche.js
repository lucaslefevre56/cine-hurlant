// public/js/recherche.js

document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("#form-recherche");
    const input = form.querySelector("input[name='q']");
    const select = form.querySelector("select[name='type']");
    const resultatsDiv = document.querySelector("#resultats-recherche");

    if (!form || !input || !select || !resultatsDiv) return;

    let timer; // pour le debounce

    // Recherche live
    input.addEventListener("keyup", () => {
        const query = input.value.trim();
        const type = select.value;

        clearTimeout(timer);

        if (query.length < 2) {
            resultatsDiv.style.display = "none";
            return;
        }

        timer = setTimeout(() => {
            lancerRecherche(query, type);
        }, 300);
    });

    // Recherche classique
    form.addEventListener("submit", (e) => {
        const query = input.value.trim();
        if (query.length < 2) {
            e.preventDefault();
        }
    });

    // Ferme les résultats si on clique en dehors
    document.addEventListener("click", (e) => {
        if (!form.contains(e.target) && !resultatsDiv.contains(e.target)) {
            resultatsDiv.style.display = "none";
        }
    });

    async function lancerRecherche(query, type) {
        try {
            const url = `${BASE_URL}/public/api/recherche.php?q=${encodeURIComponent(query)}&type=${encodeURIComponent(type)}`;
            const response = await fetch(url);
            const data = await response.json();

            resultatsDiv.style.display = "block";

            if (data.error) {
                resultatsDiv.innerHTML = `<p class="erreur">${escapeHtml(data.error)}</p>`;
                return;
            }

            let html = `
                <section class="bloc-suggestions">
                    <h3>Résultats pour : <span>${escapeHtml(data.query)}</span></h3>
            `;

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

            if (data.oeuvres.length === 0 && data.articles.length === 0) {
                html += `<p>Aucun résultat trouvé.</p>`;
            }

            html += `
                    <div style="text-align:right; margin-top: 0.5rem;">
                        <button id="fermer-resultats"
                            style="background:none;border:none;color:#999;font-size:1.2rem;cursor:pointer;"
                            aria-label="Fermer les résultats">✖️</button>
                    </div>
                </section>
            `;

            resultatsDiv.innerHTML = html;

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

    function escapeHtml(text) {
        const div = document.createElement("div");
        div.textContent = text;
        return div.innerHTML;
    }
});
