<!-- app/views/oeuvres/listeOeuvreView.php -->
*
<?php require_once ROOT . '/app/views/templates/header.php'; ?>

<!-- Titre principal de la page -->
<h2>Les Œuvres</h2>

<!-- Sous-onglets -->
<div class="subtabs" style="margin-bottom: 1rem;">
    <button class="subtab-btn active" data-subtab="films">Films</button>
    <button class="subtab-btn" data-subtab="bd">Bandes dessinées</button>
</div>

<!-- Contenu : Films -->
<div id="films" class="subtab-content">
    <!-- Je vérifie qu’il y a bien des œuvres à afficher -->
    <?php if (!empty($films)) : ?>

        <!-- J’affiche un conteneur pour toutes les cartes d’œuvres -->
        <div class="liste-oeuvres">

            <!-- Je parcours chaque œuvre pour créer une carte individuelle -->
            <?php foreach ($films as $oeuvre) : ?>
                <div class="carte-oeuvre">
                    <h3><?= htmlspecialchars($oeuvre['titre']) ?></h3>
                    <p><strong>Auteur :</strong> <?= htmlspecialchars($oeuvre['auteur']) ?></p>
                    <p><strong>Type :</strong> <?= htmlspecialchars($oeuvre['nom']) ?></p>
                    <?php if (!empty($oeuvre['genres'])) : ?>
                        <p><strong>Genres :</strong> <?= implode(', ', array_map('htmlspecialchars', $oeuvre['genres'])) ?></p>
                    <?php else : ?>
                        <p><em>Aucun genre associé</em></p>
                    <?php endif; ?>
                    <p><strong>Année :</strong> <?= htmlspecialchars($oeuvre['annee']) ?></p>
                    <?php if (!empty($oeuvre['media']) && filter_var($oeuvre['media'], FILTER_VALIDATE_URL)) : ?>
                        <img src="<?= htmlspecialchars($oeuvre['media']) ?>" alt="Visuel de <?= htmlspecialchars($oeuvre['titre']) ?>" width="200" loading="lazy">
                    <?php elseif (!empty($oeuvre['media'])) : ?>
                        <img src="<?= BASE_URL ?>/public/upload/<?= htmlspecialchars($oeuvre['media']) ?>" alt="Visuel de <?= htmlspecialchars($oeuvre['titre']) ?>" width="200" loading="lazy">
                    <?php endif; ?>
                    <p><strong>Analyse :</strong> <?= htmlspecialchars(substr($oeuvre['analyse'], 0, 150)) ?>...</p>
                    <a href="<?= BASE_URL ?>/oeuvre/fiche/<?= $oeuvre['id_oeuvre'] ?>">Voir la fiche complète</a>
                </div>
            <?php endforeach; ?>
        </div>

    <?php else : ?>
        <p>Aucun film enregistré pour le moment</p>
    <?php endif; ?>
</div>

<!-- Contenu : BD -->
<div id="bd" class="subtab-content" style="display: none;">
    <?php if (!empty($bds)) : ?>
        <div class="liste-oeuvres">
            <?php foreach ($bds as $oeuvre) : ?>
                <div class="carte-oeuvre">
                    <h3><?= htmlspecialchars($oeuvre['titre']) ?></h3>
                    <p><strong>Auteur :</strong> <?= htmlspecialchars($oeuvre['auteur']) ?></p>
                    <p><strong>Type :</strong> <?= htmlspecialchars($oeuvre['nom']) ?></p>
                    <?php if (!empty($oeuvre['genres'])) : ?>
                        <p><strong>Genres :</strong> <?= implode(', ', array_map('htmlspecialchars', $oeuvre['genres'])) ?></p>
                    <?php else : ?>
                        <p><em>Aucun genre associé</em></p>
                    <?php endif; ?>
                    <p><strong>Année :</strong> <?= htmlspecialchars($oeuvre['annee']) ?></p>
                    <?php if (!empty($oeuvre['media']) && filter_var($oeuvre['media'], FILTER_VALIDATE_URL)) : ?>
                        <img src="<?= htmlspecialchars($oeuvre['media']) ?>" alt="Visuel de <?= htmlspecialchars($oeuvre['titre']) ?>" width="200" loading="lazy">
                    <?php elseif (!empty($oeuvre['media'])) : ?>
                        <img src="<?= BASE_URL ?>/public/upload/<?= htmlspecialchars($oeuvre['media']) ?>" alt="Visuel de <?= htmlspecialchars($oeuvre['titre']) ?>" width="200" loading="lazy">
                    <?php endif; ?>
                    <p><strong>Analyse :</strong> <?= htmlspecialchars(substr($oeuvre['analyse'], 0, 150)) ?>...</p>
                    <a href="<?= BASE_URL ?>/oeuvre/fiche/<?= $oeuvre['id_oeuvre'] ?>">Voir la fiche complète</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <p>Aucune BD enregistrée pour le moment</p>
    <?php endif; ?>
</div>

<!-- Bloc de pagination -->
<div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="<?= BASE_URL ?>/oeuvre/liste?page=<?= $i ?>" class="<?= $i === $page ? 'active' : '' ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>
</div>

<script src="<?= BASE_URL ?>/public/js/listeOeuvres.js"></script>

<?php require_once ROOT . '/app/views/templates/footer.php'; ?>