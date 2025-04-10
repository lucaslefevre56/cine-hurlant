<!-- app/views/templates/layout.php -->
<?php
    require_once ROOT . '/app/views/templates/header.php';  // Charge le header
?>

<!-- Contenu principal dynamique -->
<main>
    <?php echo $content; // Affiche le contenu dynamique de la page -->?>
</main>

<?php
    require_once ROOT . '/app/views/templates/footer.php';  // Charge le footer
?>
