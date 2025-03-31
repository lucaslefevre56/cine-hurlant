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

<!-- Si un média (image) est disponible, je l’affiche uniquement lorsqu'elle entre dans la zone visible de l'ecran avec lazy loading-->
<?php if (!empty($oeuvre['media'])) : ?>
    <img src="<?= htmlspecialchars($oeuvre['media']) ?>" alt="Visuel de <?= htmlspecialchars($oeuvre['titre']) ?>" width="300" loading="lazy">
<?php endif; ?>

<!-- Partie analyse rédigée par le rédacteur -->
<h3>Analyse</h3>
<p><?= nl2br(htmlspecialchars($oeuvre['analyse'])) ?></p>

<!-- Bouton pour revenir à la liste complète des œuvres -->
<p><a href="/cine-hurlant/public/oeuvre/liste">Retour à la liste des œuvres</a></p>

<?php require_once ROOT . '/app/views/templates/footer.php'; ?>
