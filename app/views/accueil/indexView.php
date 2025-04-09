<!-- app/views/accueil/indexView.php -->

<?php require_once ROOT . '/app/views/templates/header.php'; ?>

<?php if (!empty($_SESSION['message_suppression'])) : ?>
    <p id="message-succes" style="color:green">
        <?= htmlspecialchars($_SESSION['message_suppression']) ?>
    </p>
    <?php unset($_SESSION['message_suppression']); ?>
<?php endif; ?>



<h2>Bienvenue dans Ciné-Hurlant</h2>
<p>Ça fonctionne !! 🎉</p>

<?php require_once ROOT . '/app/views/templates/footer.php'; ?>
