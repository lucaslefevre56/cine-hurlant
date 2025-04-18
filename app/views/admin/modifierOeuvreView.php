<!-- app/views/admin/modifierOeuvreView.php -->

<?php require_once ROOT . '/app/views/templates/header.php'; ?>

<h2>Modifier l'œuvre</h2>

<form id="form-ajout-modif" action="<?= BASE_URL ?>/admin/modifierOeuvre/<?= $oeuvre['id_oeuvre'] ?>" method="POST" enctype="multipart/form-data">
    <label for="titre">Titre :</label>
    <input type="text" id="titre" name="titre" value="<?= htmlspecialchars($oeuvre['titre']) ?>" required><br><br>

    <label for="auteur">Auteur :</label>
    <input type="text" id="auteur" name="auteur" value="<?= htmlspecialchars($oeuvre['auteur']) ?>" required><br><br>

    <label for="annee">Année :</label>
    <input type="number" id="annee" name="annee" value="<?= htmlspecialchars($oeuvre['annee']) ?>" required><br><br>

    <!-- Nouvelle image -->
    <label for="media">Nouvelle image (facultatif) :</label>
    <input type="file" name="media" id="media" accept="image/*"><br><br>

    <!-- Bloc JS pour message d’erreur image -->
    <div id="erreur-upload" class="message-error" style="display: none;"></div>

    <!-- Affichage de l'image actuelle si présente -->
    <?php if (!empty($oeuvre['media']) && !filter_var($oeuvre['media'], FILTER_VALIDATE_URL)) : ?>
        <p>Image actuelle :</p>
        <img src="<?= BASE_URL ?>/public/upload/<?= htmlspecialchars($oeuvre['media']) ?>" width="200" loading="lazy"><br><br>
    <?php elseif (!empty($oeuvre['media'])) : ?>
        <p>Image actuelle (URL externe) :</p>
        <img src="<?= htmlspecialchars($oeuvre['media']) ?>" width="200" loading="lazy"><br><br>
    <?php endif; ?>

    <!-- Champ caché pour transmettre le nom du fichier actuel -->
    <input type="hidden" name="media_actuelle" value="<?= htmlspecialchars($oeuvre['media']) ?>">

    <label for="video_url">URL de la vidéo :</label>
    <input type="url" id="video_url" name="video_url" value="<?= htmlspecialchars($oeuvre['video_url']) ?>"><br><br>

    <label for="analyse">Analyse :</label>
    <textarea id="analyse" name="analyse"><?= htmlspecialchars($oeuvre['analyse']) ?></textarea><br><br>

    <label for="id_type">Type :</label>
    <select name="id_type" id="id_type">
        <option value="1" <?= $oeuvre['id_type'] == 1 ? 'selected' : '' ?>>Film</option>
        <option value="2" <?= $oeuvre['id_type'] == 2 ? 'selected' : '' ?>>BD</option>
    </select><br><br>

    <button type="submit">Modifier l'œuvre</button>

    <a href="<?= BASE_URL ?>/admin/oeuvres" class="btn-annuler">Annuler</a>

</form>

<script src="<?= BASE_URL ?>/public/js/annuler.js" defer></script>
<script src="<?= BASE_URL ?>/public/js/verifierUpload.js" defer></script>

<?php require_once ROOT . '/app/views/templates/footer.php'; ?>