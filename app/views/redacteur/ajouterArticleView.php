<!-- app/views/redacteur/ajouterArticleView.php -->

<?php require_once ROOT . '/app/views/templates/header.php'; ?>

<div class="page-ajout-article">

    <!-- Affichage d’un éventuel message d’erreur -->
    <?php if (!empty($erreur)) : ?>
        <div class="message-error"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <!-- Titre principal -->
    <h2>Ajouter un article</h2>

    <!-- Formulaire d'ajout d'article avec enctype pour permettre l'upload de fichiers -->
    <form id="form-ajout-modif" method="POST" action="<?= BASE_URL ?>/redacteur/ajouterArticle" enctype="multipart/form-data">

        <!-- Titre de l’article -->
        <label for="titre">Titre</label>
        <input type="text" name="titre" id="titre" value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>" required>

        <!-- Contenu de l’article -->
        <label for="contenu">Contenu</label>
        <textarea name="contenu" id="contenu" rows="7" required><?= htmlspecialchars($_POST['contenu'] ?? '') ?></textarea>

        <!-- Image associée (upload depuis le PC) -->
        <label for="image">Image</label>
        <input type="file" name="image" id="image" accept="image/*">

        <!-- Bloc JS pour message d’erreur image -->
        <div id="erreur-upload" class="message-error" style="display: none;"></div>

        <!-- Lien vidéo (facultatif, exemple : YouTube) -->
        <label for="video_url">Lien vidéo (facultatif)</label>
        <input type="url" name="video_url" id="video_url" value="<?= htmlspecialchars($_POST['video_url'] ?? '') ?>"
            placeholder="https://www.youtube.com/watch?v=XXXXXXXXXXX">

        <!-- Séparation des œuvres en deux catégories : Films et BD -->
        <?php if (!empty($oeuvresListe)) : ?>
            <fieldset>
                <legend>Œuvres associées à l’article (facultatif)</legend>

                <!-- Films -->
                <h3>Films</h3>
                <?php foreach ($oeuvresListe as $oeuvre) : ?>
                    <?php if ($oeuvre['id_type'] == 1) : ?>
                        <label>
                            <input
                                type="checkbox"
                                name="oeuvres[]"
                                value="<?= $oeuvre['id_oeuvre'] ?>"
                                <?= in_array($oeuvre['id_oeuvre'], $_POST['oeuvres'] ?? []) ? 'checked' : '' ?>>
                            <?= htmlspecialchars($oeuvre['titre']) ?> (Film)
                        </label><br>
                    <?php endif; ?>
                <?php endforeach; ?>

                <!-- BD -->
                <h3>Bande dessinées</h3>
                <?php foreach ($oeuvresListe as $oeuvre) : ?>
                    <?php if ($oeuvre['id_type'] == 2) : ?>
                        <label>
                            <input
                                type="checkbox"
                                name="oeuvres[]"
                                value="<?= $oeuvre['id_oeuvre'] ?>"
                                <?= in_array($oeuvre['id_oeuvre'], $_POST['oeuvres'] ?? []) ? 'checked' : '' ?>>
                            <?= htmlspecialchars($oeuvre['titre']) ?> (BD)
                        </label><br>
                    <?php endif; ?>
                <?php endforeach; ?>
            </fieldset>
        <?php else : ?>
            <p><em>Aucune œuvre disponible pour le moment.</em></p>
        <?php endif; ?>

        <!-- Bouton d’envoi -->
        <button type="submit">Ajouter l’article</button>

    </form>

    <!-- Lien retour -->
    <p><a href="<?= BASE_URL ?>/">← Revenir à l’accueil</a></p>

</div>

<script src="<?= BASE_URL ?>/public/js/verifierUpload.js" defer></script>

<?php require_once ROOT . '/app/views/templates/footer.php'; ?>