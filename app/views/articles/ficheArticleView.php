<!-- app/views/articles/ficheArticleView.php -->

<?php require_once ROOT . '/app/views/templates/header.php'; ?>

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
        src="/cine-hurlant/public/images/<?= htmlspecialchars($article['image']) ?>" 
        alt="Illustration de l'article"
        class="image-article"
        width="300"
        loading="lazy"
    >
<?php endif; ?>

<!-- Si une vidéo est associée à l’article (URL), je l’affiche -->
<!-- Si une vidéo est associée à l’article, je l’intègre (YouTube) ou mets un lien -->
<?php if (!empty($article['video_url']) && preg_match('#(?:youtu\.be/|youtube\.com/watch\?v=)([\w\-]+)#', $article['video_url'], $matches)) : ?>
    <h3>Vidéo liée</h3>
    <div class="video-container">
        <iframe class="video-embed"
            src="https://www.youtube.com/embed/<?= htmlspecialchars($matches[1]) ?>"
            title="Vidéo liée à l'article"
            frameborder="0"
            allowfullscreen>
        </iframe>
    </div>
<?php elseif (!empty($article['video_url'])) : ?>
    <h3>Vidéo liée</h3>
    <p><a href="<?= htmlspecialchars($article['video_url']) ?>" target="_blank">Voir la vidéo</a></p>
<?php endif; ?>

<!-- Je montre le contenu complet de l’article, avec gestion des sauts de ligne -->
<h3>Contenu</h3>
<p><?= nl2br(htmlspecialchars($article['contenu'])) ?></p>

<!-- Je prépare deux groupes : films et BD, selon leur type -->
<?php
$films = array_filter($oeuvres, fn($o) => strtolower($o['type']) === 'film');
$bds   = array_filter($oeuvres, fn($o) => strtolower($o['type']) === 'bd');
?>

<!-- Si l’article est lié à un ou plusieurs films, je les liste -->
<?php if (!empty($films)) : ?>
    <h3>Films analysés</h3>
    <ul>
        <?php foreach ($films as $oeuvre) : ?>
            <li>
                <a href="/cine-hurlant/public/oeuvre/fiche/<?= $oeuvre['id_oeuvre'] ?>">
                    <?= htmlspecialchars($oeuvre['titre']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<!-- Si l’article est lié à des BD, je les affiche aussi -->
<?php if (!empty($bds)) : ?>
    <h3>Bandes dessinées analysées</h3>
    <ul>
        <?php foreach ($bds as $oeuvre) : ?>
            <li>
                <a href="/cine-hurlant/public/oeuvre/fiche/<?= $oeuvre['id_oeuvre'] ?>">
                    <?= htmlspecialchars($oeuvre['titre']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<!-- Si aucune œuvre n’est associée, je l’indique clairement -->
<?php if (empty($oeuvres)) : ?>
    <p><em>Aucune œuvre liée à cet article.</em></p>
<?php endif; ?>

<!-- Lien de retour vers la liste complète des articles -->
<p>
    <a href="/cine-hurlant/public/articles/liste">← Retour à la liste des articles</a>
</p>

<?php require_once ROOT . '/app/views/templates/footer.php'; ?>
