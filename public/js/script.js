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
    const burger = document.querySelector('.burger-menu');
    const menuMobile = document.querySelector('.menu-mobile');

    if (burger && menuMobile) {
        // Gestion ouverture/fermeture
        burger.addEventListener('click', (e) => {
            e.stopPropagation(); // Empêche que ça se propage au document
            menuMobile.classList.toggle('active');
            burger.classList.toggle('open');
        });

        // Clic en dehors du menu → fermeture
        document.addEventListener('click', (e) => {
            const isClickInsideMenu = menuMobile.contains(e.target);
            const isClickOnBurger = burger.contains(e.target);

            if (!isClickInsideMenu && !isClickOnBurger) {
                menuMobile.classList.remove('active');
                burger.classList.remove('open');
            }
        });

        // Clic à l'intérieur du menu → ne ferme pas
        menuMobile.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    }
}
