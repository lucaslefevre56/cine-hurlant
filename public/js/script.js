document.addEventListener('DOMContentLoaded', () => {
    const message = document.getElementById('message-succes');
    if (message) {
        setTimeout(() => {
            message.style.display = 'none';
        }, 5000); // 5 secondes
    }
});
