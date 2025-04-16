<!-- app/views/redacteur/modifierArticleView.php -->

<?php require_once ROOT . '/app/views/templates/header.php'; ?>

<h2>Modifier l'article : <?= htmlspecialchars($article['titre']) ?></h2>

<!-- Formulaire avec enctype pour permettre l'upload -->
<form action="<?= BASE_URL ?>/redacteur/modifierArticle/<?= $article['id_article'] ?>" method="POST" enctype="multipart/form-data">
    <label for="titre">Titre</label>
    <input type="text" name="titre" id="titre" value="<?= htmlspecialchars($article['titre']) ?>" required>

    <label for="contenu">Contenu</label>
    <textarea name="contenu" id="contenu" required><?= htmlspecialchars($article['contenu']) ?></textarea>

    <!-- Image actuelle affichée si présente -->
    <?php if (!empty($article['image']) && !filter_var($article['image'], FILTER_VALIDATE_URL)) : ?>
        <p>Image actuelle :</p>
        <img src="<?= BASE_URL ?>/public/upload/<?= htmlspecialchars($article['image']) ?>" width="200" loading="lazy"><br><br>
    <?php elseif (!empty($article['image'])) : ?>
        <p>Image actuelle (URL externe) :</p>
        <img src="<?= htmlspecialchars($article['image']) ?>" width="200" loading="lazy"><br><br>
    <?php endif; ?>

    <!-- Champ fichier pour nouvelle image -->
    <label for="media">Nouvelle image (facultatif) :</label>
    <input type="file" name="media" id="media" accept="image/*"><br><br>

    <!-- Champ caché pour garder l’image actuelle si aucune nouvelle n’est envoyée -->
    <input type="hidden" name="media_actuelle" value="<?= htmlspecialchars($article['image']) ?>">

    <label for="video_url">URL vidéo</label>
    <input type="url" name="video_url" id="video_url" value="<?= htmlspecialchars($article['video_url']) ?>">

    <button type="submit">Modifier</button>

    <a href="<?= BASE_URL ?>/redacteur/mesArticles" class="btn-annuler">Annuler</a>

</form>

<script src="<?= BASE_URL ?>/public/js/annuler.js" defer></script>

<?php require_once ROOT . '/app/views/templates/footer.php'; ?>
