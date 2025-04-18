// public/js/cookieConsentement.js

// Fonction utilitaire pour créer un cookie avec une durée (en jours)
// Elle construit une date d’expiration, puis crée le cookie accessible sur tout le site (path=/)
function setCookie(name, value, days) {
    const d = new Date();
    d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000)); // Conversion en millisecondes
    document.cookie = `${name}=${value};expires=${d.toUTCString()};path=/`; // Formatage du cookie
}

// Fonction utilitaire pour récupérer la valeur d’un cookie par son nom
// Si le cookie est trouvé, je retourne sa valeur, sinon je retourne null
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    return parts.length === 2 ? parts.pop().split(';').shift() : null;
}

// Fonction appelée quand l’utilisateur accepte les cookies
// Je stocke l’acceptation dans un cookie valide pendant 1 an, puis je masque la bannière
function acceptCookies() {
    setCookie('cookie_consent', 'true', 365);
    document.getElementById('cookie-banner').style.display = 'none';
}

// Fonction appelée quand l’utilisateur refuse les cookies
// Je note le refus dans un cookie (valeur "false"), et je masque également la bannière
function refuseCookies() {
    setCookie('cookie_consent', 'false', 365);
    document.getElementById('cookie-banner').style.display = 'none';
}

// Quand la page est totalement chargée (DOM prêt)
window.addEventListener('DOMContentLoaded', () => {
    const consent = getCookie('cookie_consent'); // Je vérifie s’il y a déjà une réponse de l’utilisateur

    // Si aucun cookie n’a été défini → je montre la bannière
    if (consent === null) {
        document.getElementById('cookie-banner').style.display = 'block';
    }
});
