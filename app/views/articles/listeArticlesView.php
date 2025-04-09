<!-- app/views/articles/listeArticlesView.php -->

<?php require_once ROOT . '/app/views/templates/header.php'; ?>

<!-- Titre principal de la page -->
<h2>Les articles</h2>

<!-- Je vérifie qu’il y a bien des articles à afficher -->
<?php if (!empty($articles)) : ?>

    <!-- J’affiche un conteneur pour toutes les cartes d’articles -->
    <div class="liste-articles">

        <!-- Je parcours chaque article pour créer une carte individuelle -->
        <?php foreach ($articles as $article) : ?>
            <article class="carte-article">

                <!-- Si l’article a une image, je l’affiche -->
                <?php if (!empty($article['image'])) : ?>
                    <img
                        src="<?= BASE_URL ?>/public/images/<?= htmlspecialchars($article['image']) ?>"
                        alt="Illustration de l'article"
                        class="image-article"
                        width="200"
                        loading="lazy">
                <?php endif; ?>

                <!-- Je montre le titre de l’article -->
                <h3><?= htmlspecialchars($article['titre']) ?></h3>

                <!-- J’affiche l’auteur et la date de rédaction -->
                <p>
                    <em>
                        Par <?= htmlspecialchars($article['auteur']) ?>,
                        le <?= date('d/m/Y', strtotime($article['date_redaction'])) ?>
                    </em>
                </p>

                <!-- Je vérifie si l’article est lié à une œuvre -->
                <?php if (!empty($article['titre_oeuvre'])) : ?>
                    <p>Analyse liée à :
                        <strong><?= htmlspecialchars($article['titre_oeuvre']) ?></strong>
                    </p>
                <?php endif; ?>

                <!-- J’affiche un extrait du contenu (200 premiers caractères) -->
                <p><?= nl2br(htmlspecialchars(substr($article['contenu'], 0, 200))) ?>...</p>

                <!-- Je propose un lien vers la fiche complète de l’article -->
                <a href="<?= BASE_URL ?>/article/fiche/<?= $article['id_article'] ?>">Lire la suite</a>
            </article>
        <?php endforeach; ?>
    </div>

    <!-- Bloc de pagination -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="<?= BASE_URL ?>/article/liste?page=<?= $i ?>" class="<?= $i === $page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>


    <!-- Si aucun article n’est présent, j’affiche un message d’attente -->
<?php else : ?>
    <p>Aucun article enregistré pour le moment</p>
<?php endif; ?>

<?php require_once ROOT . '/app/views/templates/footer.php'; ?>