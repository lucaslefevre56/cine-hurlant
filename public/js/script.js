// public/js/script.js

document.addEventListener('DOMContentLoaded', () => {
    autoDismissMessages(); // Dès que le DOM est chargé, je lance l'effacement automatique des messages
});

function autoDismissMessages() {
    // Je sélectionne tous les messages potentiellement concernés par la disparition automatique
    // Cela inclut les messages de succès, les messages génériques (flash), et les erreurs (sauf celle liée à l'upload dynamique)
    const messages = document.querySelectorAll('.message-success, .message-flash, .message-error:not(#erreur-upload)');

    messages.forEach(message => {
        // Je ne touche pas aux messages d’erreur gérés dynamiquement en JS (comme erreur-upload)
        if (message.id === 'erreur-upload') return;

        // Si le message est déjà caché (invisible ou fondu), je le laisse tranquille
        const computedStyle = window.getComputedStyle(message);
        if (computedStyle.display === 'none' || computedStyle.opacity === '0') return;

        // Sinon, j'attends 5 secondes avant de lancer un fondu en douceur
        setTimeout(() => {
            message.style.transition = 'opacity 0.5s ease'; // Transition fluide
            message.style.opacity = '0';                    // Début du fondu

            // Après le fondu, je masque complètement le message (display: none)
            setTimeout(() => {
                message.style.display = 'none';
            }, 500); // Temps équivalent à la durée du fondu défini plus haut
        }, 5000); // Délai avant déclenchement de la disparition (5 secondes après apparition)
    });
}
