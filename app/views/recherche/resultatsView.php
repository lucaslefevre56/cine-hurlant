<?php require_once ROOT . '/app/views/templates/header.php'; ?>

<!-- Titre principal -->
<h2>Résultats pour : "<?= htmlspecialchars($q) ?>"</h2>

<!-- Résultats des œuvres -->
<?php if (!empty($oeuvres)) : ?>
    <h3>🎬 Œuvres trouvées</h3>
    <ul>
        <?php foreach ($oeuvres as $oeuvre) : ?>
            <li>
                <strong><?= htmlspecialchars($oeuvre['titre']) ?></strong>
                par <?= htmlspecialchars($oeuvre['auteur']) ?> (<?= htmlspecialchars($oeuvre['nom']) ?>)
                - <a href="<?= BASE_URL ?>/oeuvre/fiche/<?= $oeuvre['id_oeuvre'] ?>">Voir la fiche</a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<!-- Résultats des articles -->
<?php if (!empty($articles)) : ?>
    <h3>📰 Articles trouvés</h3>
    <ul>
        <?php foreach ($articles as $article) : ?>
            <li>
                <strong><?= htmlspecialchars($article['titre']) ?></strong>
                par <?= htmlspecialchars($article['auteur']) ?>
                - <a href="<?= BASE_URL ?>/article/fiche/<?= $article['id_article'] ?>">Lire l’article</a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<!-- Aucun résultat -->
<?php if (empty($oeuvres) && empty($articles)) : ?>
    <p>Aucun résultat ne correspond à votre recherche.</p>
<?php endif; ?>

<?php require_once ROOT . '/app/views/templates/footer.php'; ?>
