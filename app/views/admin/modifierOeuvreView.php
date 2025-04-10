<!-- app/views/admin/modifierOeuvreView.php -->

<?php require_once ROOT . '/app/views/templates/header.php'; ?>

<h2>Modifier l'œuvre</h2>

<?php if (!empty($_SESSION['message'])): ?>
    <p style="color: green;"><?= htmlspecialchars($_SESSION['message']) ?></p>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<form action="<?= BASE_URL ?>/admin/modifierOeuvre/<?= $oeuvre['id_oeuvre'] ?>" method="POST">
    <label for="titre">Titre :</label>
    <input type="text" id="titre" name="titre" value="<?= htmlspecialchars($oeuvre['titre']) ?>" required><br><br>

    <label for="auteur">Auteur :</label>
    <input type="text" id="auteur" name="auteur" value="<?= htmlspecialchars($oeuvre['auteur']) ?>" required><br><br>

    <label for="annee">Année :</label>
    <input type="number" id="annee" name="annee" value="<?= htmlspecialchars($oeuvre['annee']) ?>" required><br><br>

    <label for="media">Média :</label>
    <input type="text" id="media" name="media" value="<?= htmlspecialchars($oeuvre['media']) ?>"><br><br>

    <label for="video_url">URL de la vidéo :</label>
    <input type="url" id="video_url" name="video_url" value="<?= htmlspecialchars($oeuvre['video_url']) ?>"><br><br>

    <label for="analyse">Analyse :</label>
    <textarea id="analyse" name="analyse"><?= htmlspecialchars($oeuvre['analyse']) ?></textarea><br><br>

    <label for="id_type">Type :</label>
    <select name="id_type" id="id_type">
        <!-- Option de type à remplir selon la table `type` -->
        <option value="1" <?= $oeuvre['id_type'] == 1 ? 'selected' : '' ?>>Film</option>
        <option value="2" <?= $oeuvre['id_type'] == 2 ? 'selected' : '' ?>>BD</option>
    </select><br><br>

    <button type="submit">Modifier l'œuvre</button>
</form>

<?php require_once ROOT . '/app/views/templates/footer.php'; ?>
