<!-- app/views/admin/commentairesView.php -->

<h2>Suppression des commentaires</h2>

<?php if (!empty($message)) : ?>
    <div class="message-success"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>ID</th>
            <th>Auteur</th>
            <th>Article</th>
            <th>Contenu</th>
            <th>Date</th>
            <th>Supprimer</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($commentaires as $c): ?>
            <tr>
                <td><?= $c['id_commentaire'] ?></td>
                <td><?= htmlspecialchars($c['auteur']) ?></td>
                <td><?= htmlspecialchars($c['article']) ?></td>
                <td><?= nl2br(htmlspecialchars($c['contenu'])) ?></td>
                <td><?= date('d/m/Y', strtotime($c['date_redaction'])) ?></td>
                <td>
                    <form action="<?= BASE_URL ?>/admin/commentaires" method="POST">
                        <input type="hidden" name="id_commentaire" value="<?= $c['id_commentaire'] ?>">
                        <button type="submit" class="btn btn-danger btn-supprimer">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>