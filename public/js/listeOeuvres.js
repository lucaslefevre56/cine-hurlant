// public/js/listeOeuvres.js

document.addEventListener('DOMContentLoaded', () => {
    // Je récupère tous les boutons d’onglets (ex : Films / BD)
    const buttons = document.querySelectorAll('.subtab-btn');

    // Je récupère toutes les zones de contenu associées à ces onglets
    const contents = document.querySelectorAll('.subtab-content');

    // Je définis la clé utilisée pour mémoriser l’onglet actif dans le localStorage
    const storageKey = 'onglet_oeuvre_public';

    // Si un onglet a été précédemment sélectionné, je le restaure ; sinon, je sélectionne "films" par défaut
    const ongletActif = localStorage.getItem(storageKey) || 'films';
    activerOnglet(ongletActif);

    // Pour chaque bouton d’onglet, j’ajoute un écouteur de clic
    buttons.forEach(btn => {
        btn.addEventListener('click', () => {
            const cible = btn.dataset.subtab;        // Je récupère la cible (films ou bd)
            activerOnglet(cible);                    // J’active le bon onglet
            localStorage.setItem(storageKey, cible); // Et je le mémorise dans le navigateur
        });
    });

    // Fonction utilitaire pour afficher le bon contenu et activer le bon bouton
    function activerOnglet(nom) {
        // Je mets à jour visuellement les boutons (seul le bon a la classe "active")
        buttons.forEach(btn => {
            btn.classList.toggle('active', btn.dataset.subtab === nom);
        });

        // Je n’affiche que la zone de contenu correspondant à l’onglet sélectionné
        contents.forEach(content => {
            content.style.display = content.id === nom ? 'block' : 'none';
        });
    }
});
