<?php
// app/views/erreur/error.php
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur - Ciné-Hurlant</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
</head>
<body>
    <div class="error-container">
        <h1>Une erreur est survenue</h1>
        <p><?php echo $_SESSION['error_message'] ?? "Une erreur inconnue s'est produite."; ?></p>
        <a href="<?= BASE_URL ?>" class="btn-back-home">Retour à l'accueil</a>
    </div>
</body>
</html>

<?php
// Nous supprimons le message d'erreur après l'affichage pour éviter de le réafficher lors de la prochaine requête.
unset($_SESSION['error_message']);
?>
