<h2>Modifier l'article : <?= htmlspecialchars($article['titre']) ?></h2>

<form action="" method="POST">
    <label for="titre">Titre</label>
    <input type="text" name="titre" id="titre" value="<?= htmlspecialchars($article['titre']) ?>" required>

    <label for="contenu">Contenu</label>
    <textarea name="contenu" id="contenu" required><?= htmlspecialchars($article['contenu']) ?></textarea>

    <label for="image">Image</label>
    <input type="text" name="image" id="image" value="<?= htmlspecialchars($article['image']) ?>">

    <label for="video_url">URL vidéo</label>
    <input type="text" name="video_url" id="video_url" value="<?= htmlspecialchars($article['video_url']) ?>">

    <button type="submit">Modifier</button>
</form>
