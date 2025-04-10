<!-- app/views/admin/articlesView.php -->

<h2>Gestion des articles</h2>

<?php if (!empty($message)) : ?>
    <div class="message-success"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>ID</th>
            <th>Titre</th>
            <th>Auteur</th>
            <th>Date de rédaction</th>
            <th>Voir</th>
            <th>Modifier</th>
            <th>Supprimer</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($articles as $article): ?>
            <tr>
                <td><?= $article['id_article'] ?></td>
                <td><?= htmlspecialchars($article['titre']) ?></td>
                <td><?= htmlspecialchars($article['auteur']) ?></td>
                <td><?= date('d/m/Y', strtotime($article['date_redaction'])) ?></td>
                <td>
                    <a href="<?= BASE_URL ?>/article/fiche/<?= $article['id_article'] ?>" target="_blank">Voir</a>
                </td>
                <td>
                    <a href="<?= BASE_URL ?>/admin/modifierArticle/<?= $article['id_article'] ?>">Modifier</a>
                </td>
                <td>
                    <form action="<?= BASE_URL ?>/admin/articles" method="POST" onsubmit="return confirm('Supprimer cet article ?');">
                        <input type="hidden" name="id_article" value="<?= $article['id_article'] ?>">
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>

                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>