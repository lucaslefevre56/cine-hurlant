function setCookie(name, value, days) {
    const d = new Date();
    d.setTime(d.getTime() + (days*24*60*60*1000));
    document.cookie = `${name}=${value};expires=${d.toUTCString()};path=/`;
}

function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    return parts.length === 2 ? parts.pop().split(';').shift() : null;
}

function acceptCookies() {
    setCookie('cookie_consent', 'true', 365);
    document.getElementById('cookie-banner').style.display = 'none';
}

function refuseCookies() {
    setCookie('cookie_consent', 'false', 365);
    document.getElementById('cookie-banner').style.display = 'none';
}

window.addEventListener('DOMContentLoaded', () => {
    const consent = getCookie('cookie_consent');
    if (consent === null) {
        document.getElementById('cookie-banner').style.display = 'block';
    }
});
