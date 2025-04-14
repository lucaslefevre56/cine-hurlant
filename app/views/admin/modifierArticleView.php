<!-- app/views/admin/modifierArticleView.php -->

<?php require_once ROOT . '/app/views/templates/header.php'; ?>

<h2>Modifier l'article : <?= htmlspecialchars($article['titre']) ?></h2>

<form action="" method="POST" enctype="multipart/form-data">
    <label for="titre">Titre</label>
    <input type="text" name="titre" id="titre" value="<?= htmlspecialchars($article['titre']) ?>" required>

    <label for="contenu">Contenu</label>
    <textarea name="contenu" id="contenu" required><?= htmlspecialchars($article['contenu']) ?></textarea>

    <!-- Champ pour uploader une nouvelle image -->
    <label for="image">Nouvelle image (facultatif)</label>
    <input type="file" name="image" id="image" accept="image/*"><br><br>

    <!-- Affichage de l’image actuelle -->
    <?php if (!empty($article['image']) && !filter_var($article['image'], FILTER_VALIDATE_URL)) : ?>
        <p>Image actuelle :</p>
        <img src="<?= BASE_URL ?>/public/upload/<?= htmlspecialchars($article['image']) ?>" width="200" loading="lazy"><br><br>
    <?php elseif (!empty($article['image'])) : ?>
        <p>Image actuelle (URL externe) :</p>
        <img src="<?= htmlspecialchars($article['image']) ?>" width="200" loading="lazy"><br><br>
    <?php endif; ?>

    <!-- Champ caché pour conserver l’image actuelle si aucun upload -->
    <input type="hidden" name="image_actuelle" value="<?= htmlspecialchars($article['image']) ?>">

    <label for="video_url">URL vidéo</label>
    <input type="url" name="video_url" id="video_url" value="<?= htmlspecialchars($article['video_url']) ?>">

    <button type="submit">Modifier</button>
</form>

<?php require_once ROOT . '/app/views/templates/footer.php'; ?>
