document.addEventListener('DOMContentLoaded', () => {
    autoDismissMessages();
});

function autoDismissMessages() {
    const messages = document.querySelectorAll('.message-success, .message-error, .message-flash');

    messages.forEach(message => {
        setTimeout(() => {
            message.style.transition = 'opacity 0.5s ease';
            message.style.opacity = '0';
            setTimeout(() => {
                message.style.display = 'none';
            }, 500);
        }, 5000);
    });
}
