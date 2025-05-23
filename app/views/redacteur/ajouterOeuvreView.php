<!-- app/views/redacteur/ajouterOeuvreView.php -->

<?php require_once ROOT . '/app/views/templates/header.php'; ?>

<div class="page-ajout-oeuvre">

    <?php if (!empty($erreur)) : ?>
        <div class="message-error"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <h2>Ajouter une œuvre</h2>

    <!-- Formulaire avec enctype pour permettre l'upload de fichiers -->
    <form id="form-ajout-modif" method="POST" action="<?= BASE_URL ?>/redacteur/ajouterOeuvre" enctype="multipart/form-data">

        <!-- Titre -->
        <label for="titre">Titre</label>
        <input type="text" name="titre" id="titre" value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>" required>

        <!-- Auteur(s) -->
        <label for="auteur">Auteur(s)</label>
        <input type="text" name="auteur" id="auteur" value="<?= htmlspecialchars($_POST['auteur'] ?? '') ?>" required>

        <!-- Type d'œuvre -->
        <label for="type">Type</label>
        <select name="type" id="type" required>
            <option value="">-- Choisir --</option>
            <option value="film" <?= ($_POST['type'] ?? '') === 'film' ? 'selected' : '' ?>>Film</option>
            <option value="bd" <?= ($_POST['type'] ?? '') === 'bd' ? 'selected' : '' ?>>Bande dessinée</option>
        </select>

        <!-- Genres -->
        <fieldset>
            <legend>Genres</legend>

            <label>
                <input type="checkbox" name="genres[]" value="1"
                    <?= in_array('1', $_POST['genres'] ?? []) ? 'checked' : '' ?>> Science-fiction
            </label><br>

            <label>
                <input type="checkbox" name="genres[]" value="2"
                    <?= in_array('2', $_POST['genres'] ?? []) ? 'checked' : '' ?>> Fantastique
            </label><br>

            <label>
                <input type="checkbox" name="genres[]" value="3"
                    <?= in_array('3', $_POST['genres'] ?? []) ? 'checked' : '' ?>> Western
            </label><br>

            <label>
                <input type="checkbox" name="genres[]" value="4"
                    <?= in_array('4', $_POST['genres'] ?? []) ? 'checked' : '' ?>> Cyberpunk
            </label><br>

            <label>
                <input type="checkbox" name="genres[]" value="5"
                    <?= in_array('5', $_POST['genres'] ?? []) ? 'checked' : '' ?>> Horreur
            </label><br>

            <label>
                <input type="checkbox" name="genres[]" value="6"
                    <?= in_array('6', $_POST['genres'] ?? []) ? 'checked' : '' ?>> Comédie
            </label><br>
        </fieldset>

        <!-- Année -->
        <label for="annee">Année</label>
        <input type="number" name="annee" id="annee" min="1900" max="2100"
            value="<?= htmlspecialchars($_POST['annee'] ?? '') ?>" required>

        <!-- Analyse -->
        <label for="analyse">Analyse</label>
        <textarea name="analyse" id="analyse" rows="5" required><?= htmlspecialchars($_POST['analyse'] ?? '') ?></textarea>

        <!-- Média associé (upload de l'image) -->
        <label for="image">Image</label>
        <input type="file" name="media" id="image" accept="image/*" required>

        <!-- Bloc JS pour message d’erreur image -->
        <div id="erreur-upload" class="message-error" style="display: none;"></div>


        <!-- Lien vidéo (facultatif, exemple : YouTube) -->
        <label for="video_url">Lien vidéo (facultatif)</label>
        <input type="url" name="video_url" id="video_url" value="<?= htmlspecialchars($_POST['video_url'] ?? '') ?>"
            placeholder="https://www.youtube.com/watch?v=XXXXXXXXXXX">

        <!-- Bouton -->
        <button type="submit">Ajouter l'œuvre</button>

    </form>

    <p><a href="<?= BASE_URL ?>/">← Revenir à l’accueil</a></p>

</div>

<script src="<?= BASE_URL ?>/public/js/verifierUpload.js" defer></script>

<?php require_once ROOT . '/app/views/templates/footer.php'; ?>