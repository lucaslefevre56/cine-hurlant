<!-- app/views/redacteur/mesOeuvresView.php -->

<div class="redacteur-oeuvres">

    <?php if (!empty($message)) : ?>
        <div class="message-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <h2>Mes œuvres</h2>

    <!-- Sous-onglets -->
    <div class="subtabs">
        <button class="subtab-btn active" data-subtab="films">Films</button>
        <button class="subtab-btn" data-subtab="bd">Bandes dessinées</button>
    </div>

    <!-- Contenu : Films -->
    <div id="films" class="subtab-content">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Auteur</th>
                    <th>Type</th>
                    <th>Année</th>
                    <th>Voir</th>
                    <th>Modifier</th>
                    <th>Supprimer</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($films)) : ?>
                    <?php foreach ($films as $oeuvre) : ?>
                        <tr>
                            <td data-label="ID"><?= $oeuvre['id_oeuvre'] ?></td>
                            <td data-label="Titre"><?= htmlspecialchars($oeuvre['titre']) ?></td>
                            <td data-label="Auteur"><?= htmlspecialchars($oeuvre['auteur']) ?></td>
                            <td data-label="Type"><?= htmlspecialchars(ucfirst($oeuvre['nom'])) ?></td>
                            <td data-label="Année"><?= htmlspecialchars($oeuvre['annee']) ?></td>
                            <td>
                                <a href="<?= BASE_URL ?>/oeuvre/fiche/<?= $oeuvre['id_oeuvre'] ?>" target="_blank">Voir</a>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>/redacteur/modifierOeuvre/<?= $oeuvre['id_oeuvre'] ?>">Modifier</a>
                            </td>
                            <td>
                                <form action="<?= BASE_URL ?>/redacteur/supprimerOeuvre/<?= $oeuvre['id_oeuvre'] ?>" method="POST">
                                    <button type="submit" class="btn btn-danger btn-supprimer">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="8">Aucun film enregistré.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Contenu : BD -->
    <div id="bd" class="subtab-content" style="display: none;">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Auteur</th>
                    <th>Type</th>
                    <th>Année</th>
                    <th>Voir</th>
                    <th>Modifier</th>
                    <th>Supprimer</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($bds)) : ?>
                    <?php foreach ($bds as $oeuvre) : ?>
                        <tr>
                            <td data-label="ID"><?= $oeuvre['id_oeuvre'] ?></td>
                            <td data-label="Titre"><?= htmlspecialchars($oeuvre['titre']) ?></td>
                            <td data-label="Auteur"><?= htmlspecialchars($oeuvre['auteur']) ?></td>
                            <td data-label="Type"><?= htmlspecialchars(ucfirst($oeuvre['nom'])) ?></td>
                            <td data-label="Année"><?= htmlspecialchars($oeuvre['annee']) ?></td>
                            <td>
                                <a href="<?= BASE_URL ?>/oeuvre/fiche/<?= $oeuvre['id_oeuvre'] ?>" target="_blank">Voir</a>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>/redacteur/modifierOeuvre/<?= $oeuvre['id_oeuvre'] ?>">Modifier</a>
                            </td>
                            <td>
                                <form action="<?= BASE_URL ?>/redacteur/supprimerOeuvre/<?= $oeuvre['id_oeuvre'] ?>" method="POST">
                                    <button type="submit" class="btn btn-danger btn-supprimer">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="8">Aucune BD enregistrée.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>