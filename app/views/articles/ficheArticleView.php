<?php $loadCommentairesJs = true; ?>
<?php require_once ROOT . '/app/views/templates/header.php'; ?>

<div class="fiche-article">

    <!-- J’affiche le titre principal de l’article -->
    <h2><?= htmlspecialchars($article['titre']) ?></h2>

    <!-- Je précise le nom de l’auteur et la date de publication -->
    <p>
        <strong>Auteur :</strong> <?= htmlspecialchars($article['auteur']) ?>,
        le <?= date('d/m/Y', strtotime($article['date_redaction'])) ?>
    </p>

    <!-- Si une image est associée à l’article, je l’affiche -->
    <?php if (!empty($article['image'])) : ?>
        <img
            src="<?= BASE_URL ?>/public/upload/<?= htmlspecialchars($article['image']) ?>"
            alt="Illustration de l'article"
            class="image-article"
            loading="lazy">
    <?php endif; ?>

    <!-- Vidéo intégrée ou lien externe -->
    <?php if (!empty($article['video_url']) && preg_match('#(?:youtu\.be/|youtube\.com/watch\?v=)([\w\-]+)#', $article['video_url'], $matches)) : ?>
        <h3>Vidéo liée</h3>
        <div class="video-container">
            <iframe class="video-embed"
                src="https://www.youtube-nocookie.com/embed/<?= htmlspecialchars($matches[1]) ?>"

                title="Vidéo liée à l'article"
                frameborder="0"
                allowfullscreen>
            </iframe>
        </div>
    <?php elseif (!empty($article['video_url'])) : ?>
        <h3>Vidéo liée</h3>
        <p><a href="<?= htmlspecialchars($article['video_url']) ?>" target="_blank">Voir la vidéo</a></p>
    <?php endif; ?>

    <!-- Contenu principal -->
    <p><?= nl2br(htmlspecialchars($article['contenu'])) ?></p>

    <!-- Œuvres liées -->
    <?php
    $films = array_filter($oeuvres, fn($o) => strtolower($o['type']) === 'film');
    $bds   = array_filter($oeuvres, fn($o) => strtolower($o['type']) === 'bd');
    ?>

    <?php if (!empty($films)) : ?>
        <h3>Films analysés</h3>
        <ul>
            <?php foreach ($films as $oeuvre) : ?>
                <li>
                    <a href="<?= BASE_URL ?>/oeuvre/fiche/<?= $oeuvre['id_oeuvre'] ?>">
                        <?= htmlspecialchars($oeuvre['titre']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if (!empty($bds)) : ?>
        <h3>Bandes dessinées analysées</h3>
        <ul>
            <?php foreach ($bds as $oeuvre) : ?>
                <li>
                    <a href="<?= BASE_URL ?>/oeuvre/fiche/<?= $oeuvre['id_oeuvre'] ?>">
                        <?= htmlspecialchars($oeuvre['titre']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if (empty($oeuvres)) : ?>
        <p><em>Aucune œuvre liée à cet article.</em></p>
    <?php endif; ?>

    <!-- Commentaires -->
    <section id="zone-commentaires">
        <h2>Commentaires</h2>

        <div id="commentaires-liste">
            <?php foreach ($commentaires as $com): ?>
                <div class="commentaire"
                    data-id="<?= $com['id_commentaire'] ?>"
                    data-supprimable="<?= ($_SESSION['user']['id'] ?? null) == $com['id_utilisateur'] || ($_SESSION['user']['role'] ?? '') === 'admin' ? 'true' : 'false' ?>">
                    <p>
                        <strong><?= htmlspecialchars($com['auteur']) ?></strong>
                        — <?= $com['date_redaction'] ?>
                    </p>
                    <p class="contenu-commentaire"><?= nl2br(htmlspecialchars($com['contenu'])) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <?php if (isset($_SESSION['user'])): ?>
        <form id="form-commentaire">
            <textarea name="contenu" id="contenu" rows="4" required placeholder="Écris ton commentaire ici..."></textarea>
            <input type="hidden" name="id_article" value="<?= $article['id_article'] ?>">
            <button type="submit">Envoyer</button>
        </form>
    <?php else: ?>
        <p class="info-connexion">Tu dois être connecté pour poster un commentaire.</p>
    <?php endif; ?>

    <div id="message-commentaire" class="message-flash"></div>

    <!-- Lien retour -->
    <div class="retour">
        <p><a href="<?= BASE_URL ?>/article/liste">← Retour à la liste des articles</a></p>
        <p><a href="<?= BASE_URL ?>/">← Revenir à l’accueil</a></p>
    </div>

</div>

<?php require_once ROOT . '/app/views/templates/footer.php'; ?>