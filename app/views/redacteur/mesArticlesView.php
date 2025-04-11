<!-- app/views/redacteur/mesArticlesView.php -->

<div class="section-redacteur">
    <h3>Mes articles</h3>

    <?php if (!empty($message)) : ?>
        <p class="message-success"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <?php if (empty($articles)) : ?>
        <p class="message-info">Vous n'avez encore rédigé aucun article.</p>
    <?php else : ?>
        <?php foreach ($articles as $article) : ?>
            <div class="article-item">
                <h4><?= htmlspecialchars($article['titre']) ?></h4>
                <p><?= nl2br(htmlspecialchars(substr($article['contenu'], 0, 200))) ?>...</p>

                <div class="article-actions">
                    <a href="<?= BASE_URL ?>/redacteur/modifierArticle/<?= $article['id'] ?>" class="btn-modifier">✏️ Modifier</a>

                    <form method="POST" action="<?= BASE_URL ?>/redacteur/supprimerArticle/<?= $article['id'] ?>" style="display:inline;">
                        <button type="submit" class="btn-supprimer" onclick="return confirm('Supprimer cet article ?')">🗑️ Supprimer</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
