<!-- app/views/redacteur/modifierOeuvreView.php -->

<?php require_once ROOT . '/app/views/templates/header.php'; ?>

<div class="page-modifier-oeuvre">

    <h2>Modifier l'œuvre</h2>

    <form id="form-ajout-modif" action="<?= BASE_URL ?>/redacteur/modifierOeuvre/<?= $oeuvre['id_oeuvre'] ?>" method="POST" enctype="multipart/form-data">

        <label for="titre">Titre :</label>
        <input type="text" id="titre" name="titre" value="<?= htmlspecialchars($oeuvre['titre']) ?>" required>

        <label for="auteur">Auteur :</label>
        <input type="text" id="auteur" name="auteur" value="<?= htmlspecialchars($oeuvre['auteur']) ?>" required>

        <label for="annee">Année :</label>
        <input type="number" id="annee" name="annee" value="<?= htmlspecialchars($oeuvre['annee']) ?>" required>

        <!-- Upload facultatif d'une nouvelle image -->
        <label for="media">Nouvelle image (facultatif) :</label>
        <input type="file" name="media" id="media" accept="image/*">

        <!-- Bloc JS pour message d’erreur image -->
        <div id="erreur-upload" class="message-error" style="display: none;"></div>

        <!-- Champ caché avec l'image actuelle -->
        <input type="hidden" name="media_actuelle" value="<?= htmlspecialchars($oeuvre['media']) ?>">

        <!-- Affichage image actuelle -->
        <?php if (!empty($oeuvre['media']) && !filter_var($oeuvre['media'], FILTER_VALIDATE_URL)) : ?>
            <p>Image actuelle :</p>
            <img src="<?= BASE_URL ?>/public/upload/<?= htmlspecialchars($oeuvre['media']) ?>" width="200" loading="lazy">
        <?php elseif (!empty($oeuvre['media'])) : ?>
            <p>Image actuelle (URL externe) :</p>
            <img src="<?= htmlspecialchars($oeuvre['media']) ?>" width="200" loading="lazy">
        <?php endif; ?>

        <label for="video_url">URL de la vidéo :</label>
        <input type="url" id="video_url" name="video_url" value="<?= htmlspecialchars($oeuvre['video_url']) ?>">

        <label for="analyse">Analyse :</label>
        <textarea id="analyse" name="analyse"><?= htmlspecialchars($oeuvre['analyse']) ?></textarea>

        <label for="id_type">Type :</label>
        <select name="id_type" id="id_type">
            <option value="1" <?= $oeuvre['id_type'] == 1 ? 'selected' : '' ?>>Film</option>
            <option value="2" <?= $oeuvre['id_type'] == 2 ? 'selected' : '' ?>>BD</option>
        </select>

        <button type="submit">Modifier l'œuvre</button>

        <a href="<?= BASE_URL ?>/redacteur/mesOeuvres" class="btn-annuler">Annuler</a>

    </form>

</div>

<script src="<?= BASE_URL ?>/public/js/annuler.js" defer></script>
<script src="<?= BASE_URL ?>/public/js/verifierUpload.js" defer></script>

<?php require_once ROOT . '/app/views/templates/footer.php'; ?>
