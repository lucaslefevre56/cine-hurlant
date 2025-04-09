<!-- app/views/erreur/404.php -->

<?php 
// J'inclus le header du site pour garder une mise en page cohérente
require_once ROOT . '/app/views/templates/header.php'; 
?>

<div class="erreur-404" style="text-align: center; padding: 50px;">
    <!-- Titre principal de la page 404 -->
    <h1>404 - Page introuvable</h1>

    <!-- Message générique expliquant que la page n'existe pas -->
    <p>Cette page n’existe pas ou n’est plus disponible.</p>

    <?php if (!empty($erreur)) : ?>
        <!-- Si un message d'erreur personnalisé a été transmis, je l’affiche ici -->
        <p style="color: darkred;"><strong>Détail :</strong> <?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>

    <!-- Lien de retour vers la page d’accueil -->
    <p><a href="<?= BASE_URL ?>/">Retour à l’accueil</a></p>
</div>

<?php 
// J'inclus le footer pour terminer proprement la page
require_once ROOT . '/app/views/templates/footer.php'; 
?>
