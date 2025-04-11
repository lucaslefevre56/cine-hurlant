document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('.subtab-btn');
    const contents = document.querySelectorAll('.subtab-content');
    const storageKey = 'onglet_oeuvre_public';

    // Restaure l'onglet précédemment actif s'il existe
    const ongletActif = localStorage.getItem(storageKey) || 'films';
    activerOnglet(ongletActif);

    // Ajoute l'écouteur sur chaque bouton
    buttons.forEach(btn => {
        btn.addEventListener('click', () => {
            const cible = btn.dataset.subtab;
            activerOnglet(cible);
            localStorage.setItem(storageKey, cible);
        });
    });

    function activerOnglet(nom) {
        // Active le bon bouton
        buttons.forEach(btn => {
            btn.classList.toggle('active', btn.dataset.subtab === nom);
        });

        // Affiche uniquement le contenu correspondant
        contents.forEach(content => {
            content.style.display = content.id === nom ? 'block' : 'none';
        });
    }
});
