// public/js/script.js

document.addEventListener('DOMContentLoaded', () => {
    autoDismissMessages();   // Dès que le DOM est prêt, je lance la disparition automatique des messages
    initBurgerMenu();        // Et j’ajoute le comportement du menu burger si on est sur mobile
});

function autoDismissMessages() {
    // Je sélectionne tous les messages qui doivent potentiellement disparaître (sauf ceux gérés dynamiquement comme erreur-upload)
    const messages = document.querySelectorAll('.message-success, .message-flash, .message-error:not(#erreur-upload)');

    messages.forEach(message => {
        if (message.id === 'erreur-upload') return; // Je laisse les erreurs d’upload visibles (elles sont gérées ailleurs)

        const computedStyle = window.getComputedStyle(message);
        if (computedStyle.display === 'none' || computedStyle.opacity === '0') return; // Si déjà invisible, je ne fais rien

        // Sinon, je déclenche un fondu au bout de 5 secondes
        setTimeout(() => {
            message.style.transition = 'opacity 0.5s ease';
            message.style.opacity = '0';

            // Une fois le fondu terminé, je le masque complètement
            setTimeout(() => {
                message.style.display = 'none';
            }, 500);
        }, 5000);
    });
}

function initBurgerMenu() {
    // Je sélectionne les deux éléments essentiels : le bouton burger et le menu mobile
    const burger = document.querySelector('.burger-menu');
    const menuMobile = document.querySelector('.menu-mobile');

    // Si les deux sont présents dans le DOM, je lance la magie
    if (burger && menuMobile) {
        burger.addEventListener('click', () => {
            // J’alterne l’état actif du menu (affiché / masqué)
            menuMobile.classList.toggle('active');

            // Et j’ajoute aussi une classe au bouton burger pour pouvoir le styliser (croix, rotation, couleur…)
            burger.classList.toggle('open');
        });
    }
}
