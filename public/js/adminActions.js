// public/js/adminActions.js

document.addEventListener("DOMContentLoaded", () => {
    const adminContent = document.getElementById("admin-content");

    // Interception de toutes les soumissions liées au panneau admin
    adminContent.addEventListener("submit", function (e) {
        const form = e.target;

        // Cas classiques : suppression ou changement de rôle
        if (
            form.action.includes("/admin/changerRole") ||
            form.action.includes("/admin/supprimerUtilisateur") ||
            form.action.includes("/admin/articles") ||
            form.action.includes("/admin/oeuvres") ||
            form.action.includes("/admin/commentaires")
        ) {
            e.preventDefault();

            const formData = new FormData(form);

            fetch(form.action, {
                method: "POST",
                body: formData
            })
                .then(response => {
                    if (!response.ok) throw new Error("Erreur serveur.");
                    return response.text();
                })
                .then(html => {
                    adminContent.innerHTML = html;

                    // Recharge les sous-onglets films/BD s’ils sont présents
                    const boutonDefaut = document.querySelector('.subtab-btn[data-subtab="films"]');
                    if (boutonDefaut) boutonDefaut.click();
                })
                .catch(error => {
                    adminContent.innerHTML = `<p style="color:red;">${error.message}</p>`;
                });
        }

        // Cas spécifiques : modification d’article ou d’œuvre
        if (
            form.action.includes("/admin/modifierOeuvre/") ||
            form.action.includes("/admin/modifierArticle/")
        ) {
            e.preventDefault();

            const formData = new FormData(form);

            fetch(form.action, {
                method: "POST",
                body: formData
            })
                .then(response => {
                    if (!response.ok) throw new Error("Erreur lors de la modification.");
                    return response.text();
                })
                .then(html => {
                    adminContent.innerHTML = html;

                    // Recharge les sous-onglets si c’est une œuvre
                    if (form.action.includes("modifierOeuvre")) {
                        const boutonDefaut = document.querySelector('.subtab-btn[data-subtab="films"]');
                        if (boutonDefaut) boutonDefaut.click();
                    }
                })
                .catch(error => {
                    adminContent.innerHTML = `<p style="color:red;">${error.message}</p>`;
                });
        }
    });
});
