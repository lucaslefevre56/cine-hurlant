<!-- app/views/redacteur/mesArticlesView.php -->

<div class="redacteur-articles">

    <h2>Mes articles</h2>

    <?php if (!empty($message)) : ?>
        <div class="message-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Date de rédaction</th>
                <th>Voir</th>
                <th>Modifier</th>
                <th>Supprimer</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($articles)) : ?>
                <?php foreach ($articles as $article): ?>
                    <tr>
                        <td data-label="ID"><?= $article['id_article'] ?></td>
                        <td data-label="Titre"><?= htmlspecialchars($article['titre']) ?></td>
                        <td data-label="Date de rédaction"><?= date('d/m/Y', strtotime($article['date_redaction'])) ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/article/fiche/<?= $article['id_article'] ?>" target="_blank">Voir</a>
                        </td>
                        <td>
                            <a href="<?= BASE_URL ?>/redacteur/modifierArticle/<?= $article['id_article'] ?>">Modifier</a>
                        </td>
                        <td>
                            <form action="<?= BASE_URL ?>/redacteur/supprimerArticle/<?= $article['id_article'] ?>" method="POST">
                                <button type="submit" class="btn btn-danger btn-supprimer">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="6">Aucun article rédigé pour le moment.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>