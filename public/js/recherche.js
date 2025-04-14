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

        clearTimeout(timer); // réinitialise le délai

        if (query.length < 2) {
            resultatsDiv.style.display = "none";
            return;
        }

        // Attend 300ms après la dernière frappe
        timer = setTimeout(() => {
            lancerRecherche(query, type);
        }, 300);
    });

    // Recherche classique à la soumission (Enter ou bouton)
    form.addEventListener("submit", (e) => {
        e.preventDefault();
        const query = input.value.trim();
        const type = select.value;
        lancerRecherche(query, type);
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

            let html = `<p>Résultats pour : <strong>${escapeHtml(data.query)}</strong></p>`;

            if (data.oeuvres.length > 0) {
                html += `<h2>Œuvres</h2><ul>`;
                data.oeuvres.forEach(o => {
                    html += `<li><a href="${BASE_URL}/oeuvre/fiche/${o.id_oeuvre}">${escapeHtml(o.titre)}</a> (${escapeHtml(o.type)})</li>`;
                });
                html += `</ul>`;
            }

            if (data.articles.length > 0) {
                html += `<h2>Articles</h2><ul>`;
                data.articles.forEach(a => {
                    html += `<li><a href="${BASE_URL}/article/fiche/${a.id_article}">${escapeHtml(a.titre)}</a> par ${escapeHtml(a.auteur)}</li>`;
                });
                html += `</ul>`;
            }

            if (data.oeuvres.length === 0 && data.articles.length === 0) {
                html += `<p>Aucun résultat trouvé.</p>`;
            }

            resultatsDiv.innerHTML = html;

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
