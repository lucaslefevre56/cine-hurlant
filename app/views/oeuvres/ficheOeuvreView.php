<!-- app/views/oeuvres/ficheOeuvreView.php -->

<?php require_once ROOT . '/app/views/templates/header.php'; ?>

<!-- Je commence par afficher le titre de l’œuvre -->
<h2><?= htmlspecialchars($oeuvre['titre']) ?></h2>

<!-- J'affiche l’auteur ou le créateur -->
<p><strong>Auteur :</strong> <?= htmlspecialchars($oeuvre['auteur']) ?></p>

<!-- J’affiche le type d’œuvre (film, BD...) -->
<p><strong>Type :</strong> <?= htmlspecialchars($oeuvre['nom']) ?></p>

<!-- Si des genres sont associés à cette œuvre, je les affiche séparés par des virgules -->
<?php if (!empty($genres)) : ?>
    <p><strong>Genres :</strong> <?= implode(', ', array_map('htmlspecialchars', $genres)) ?></p>
<?php else : ?>
    <!-- Sinon, je précise qu’aucun genre n’est lié -->
    <p><em>Aucun genre associé</em></p>
<?php endif; ?>

<!-- J’affiche l’année de création/publication -->
<p><strong>Année :</strong> <?= htmlspecialchars($oeuvre['annee']) ?></p>

<!-- Affichage de l’image associée si elle existe -->
<?php if (!empty($oeuvre['media']) && filter_var($oeuvre['media'], FILTER_VALIDATE_URL)) : ?>
    <!-- Si le média est une URL externe valide, on l’utilise telle quelle -->
    <img src="<?= htmlspecialchars($oeuvre['media']) ?>" alt="Visuel de <?= htmlspecialchars($oeuvre['titre']) ?>" width="200" loading="lazy">
<?php elseif (!empty($oeuvre['media'])) : ?>
    <!-- Sinon, on considère qu’il s’agit d’une image locale dans /public/upload -->
    <img src="<?= BASE_URL ?>/public/upload/<?= htmlspecialchars($oeuvre['media']) ?>" alt="Visuel de <?= htmlspecialchars($oeuvre['titre']) ?>" width="300" loading="lazy">
<?php endif; ?>

<!-- Si une vidéo est associée à cette œuvre, je l'affiche -->
<?php if (!empty($oeuvre['video_url']) && preg_match('#(?:youtu\.be/|youtube\.com/watch\?v=)([\w\-]+)#', $oeuvre['video_url'], $matches)) : ?>
    <h3>Vidéo associée</h3>
    <iframe width="560" height="315"
        src="https://www.youtube.com/embed/<?= htmlspecialchars($matches[1]) ?>"
        title="Vidéo associée"
        frameborder="0"
        allowfullscreen>
    </iframe>
<?php else : ?>
    <?php if (!empty($oeuvre['video_url'])) : ?>
        <p><a href="<?= htmlspecialchars($oeuvre['video_url']) ?>" target="_blank">Voir la vidéo</a></p>
    <?php endif; ?>
<?php endif; ?>


<!-- Partie analyse rédigée par le rédacteur -->
<h3>Analyse</h3>
<p><?= nl2br(htmlspecialchars($oeuvre['analyse'])) ?></p>

<!-- Bouton pour revenir à la liste complète des œuvres -->
<p><a href="<?= BASE_URL ?>/oeuvre/liste">Retour à la liste des œuvres</a></p>

<?php require_once ROOT . '/app/views/templates/footer.php'; ?>