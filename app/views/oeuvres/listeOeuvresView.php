<!-- app/views/oeuvres/ficheOeuvreView.php -->

<?php require_once ROOT . '/app/views/templates/header.php'; ?>

<div class="oeuvres">

<!-- Titre principal de la page -->
<h2>Les Œuvres</h2>

<!-- Sous-onglets -->
<div class="subtabs" style="margin-bottom: 1rem;">
    <button class="subtab-btn active" data-subtab="films">Films</button>
    <button class="subtab-btn" data-subtab="bd">Bandes dessinées</button>
</div>

<!-- Contenu : Films -->
<div id="films" class="subtab-content">
    <!-- Bloc de pagination des films (haut) -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPagesFilms; $i++): ?>
            <a href="<?= BASE_URL ?>/oeuvre/liste?pageFilms=<?= $i ?>&pageBD=<?= $pageBD ?>" class="<?= $i === $pageFilms ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>

    <!-- Je vérifie qu’il y a bien des œuvres à afficher -->
    <?php if (!empty($films)) : ?>
        <!-- J’affiche un conteneur pour toutes les cartes d’œuvres -->
        <div class="liste-oeuvres">
            <?php foreach ($films as $oeuvre) : ?>
                <div class="carte-oeuvre">
                    <!-- J’affiche l’image de l’œuvre si elle existe -->
                    <?php if (!empty($oeuvre['media']) && filter_var($oeuvre['media'], FILTER_VALIDATE_URL)) : ?>
                        <img src="<?= htmlspecialchars($oeuvre['media']) ?>" alt="Visuel de <?= htmlspecialchars($oeuvre['titre']) ?>" loading="lazy">
                    <?php elseif (!empty($oeuvre['media'])) : ?>
                        <img src="<?= BASE_URL ?>/public/upload/<?= htmlspecialchars($oeuvre['media']) ?>" alt="Visuel de <?= htmlspecialchars($oeuvre['titre']) ?>" loading="lazy">
                    <?php endif; ?>

                    <!-- Bloc d'informations principales -->
                    <div class="infos-oeuvre">
                        <h3><?= htmlspecialchars($oeuvre['titre']) ?></h3>
                        <p><strong>Réalisateur :</strong> <?= htmlspecialchars($oeuvre['auteur']) ?></p>
                        <p><strong>Type :</strong> <?= htmlspecialchars($oeuvre['nom']) ?></p>
                        <?php if (!empty($oeuvre['genres'])) : ?>
                            <p><strong>Genres :</strong> <?= implode(', ', array_map('htmlspecialchars', $oeuvre['genres'])) ?></p>
                        <?php else : ?>
                            <p><em>Aucun genre associé</em></p>
                        <?php endif; ?>
                        <p><strong>Année :</strong> <?= htmlspecialchars($oeuvre['annee']) ?></p>

                        <!-- J’utilise mb_substr pour éviter les caractères cassés -->
                        <p class="analyse"><?= htmlspecialchars(mb_substr($oeuvre['analyse'], 0, 150)) ?>...</p>
                    </div>

                    <!-- Lien vers la fiche complète -->
                    <a href="<?= BASE_URL ?>/oeuvre/fiche/<?= $oeuvre['id_oeuvre'] ?>">Voir la fiche complète</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <p>Aucun film enregistré pour le moment</p>
    <?php endif; ?>

    <!-- Bloc de pagination des films (bas) -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPagesFilms; $i++): ?>
            <a href="<?= BASE_URL ?>/oeuvre/liste?pageFilms=<?= $i ?>&pageBD=<?= $pageBD ?>" class="<?= $i === $pageFilms ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
</div>

<!-- Contenu : BD -->
<div id="bd" class="subtab-content" style="display: none;">
    <!-- Bloc de pagination des BD (haut) -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPagesBD; $i++): ?>
            <a href="<?= BASE_URL ?>/oeuvre/liste?pageFilms=<?= $pageFilms ?>&pageBD=<?= $i ?>" class="<?= $i === $pageBD ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>

    <!-- Je vérifie qu’il y a bien des œuvres à afficher -->
    <?php if (!empty($bds)) : ?>
        <!-- J’affiche un conteneur pour toutes les cartes d’œuvres -->
        <div class="liste-oeuvres">
            <?php foreach ($bds as $oeuvre) : ?>
                <div class="carte-oeuvre">
                    <!-- J’affiche l’image de l’œuvre si elle existe -->
                    <?php if (!empty($oeuvre['media']) && filter_var($oeuvre['media'], FILTER_VALIDATE_URL)) : ?>
                        <img src="<?= htmlspecialchars($oeuvre['media']) ?>" alt="Visuel de <?= htmlspecialchars($oeuvre['titre']) ?>" loading="lazy">
                    <?php elseif (!empty($oeuvre['media'])) : ?>
                        <img src="<?= BASE_URL ?>/public/upload/<?= htmlspecialchars($oeuvre['media']) ?>" alt="Visuel de <?= htmlspecialchars($oeuvre['titre']) ?>" loading="lazy">
                    <?php endif; ?>

                    <!-- Bloc d'informations principales -->
                    <div class="infos-oeuvre">
                        <h3><?= htmlspecialchars($oeuvre['titre']) ?></h3>
                        <p><strong>Auteur :</strong> <?= htmlspecialchars($oeuvre['auteur']) ?></p>
                        <p><strong>Type :</strong> <?= htmlspecialchars($oeuvre['nom']) ?></p>
                        <?php if (!empty($oeuvre['genres'])) : ?>
                            <p><strong>Genres :</strong> <?= implode(', ', array_map('htmlspecialchars', $oeuvre['genres'])) ?></p>
                        <?php else : ?>
                            <p><em>Aucun genre associé</em></p>
                        <?php endif; ?>
                        <p><strong>Année :</strong> <?= htmlspecialchars($oeuvre['annee']) ?></p>

                        <!-- J’utilise mb_substr pour éviter les caractères cassés -->
                        <p class="analyse"><?= htmlspecialchars(mb_substr($oeuvre['analyse'], 0, 150)) ?>...</p>
                    </div>

                    <!-- Lien vers la fiche complète -->
                    <a href="<?= BASE_URL ?>/oeuvre/fiche/<?= $oeuvre['id_oeuvre'] ?>">Voir la fiche complète</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <p>Aucune BD enregistrée pour le moment</p>
    <?php endif; ?>

    <!-- Bloc de pagination des BD (bas) -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPagesBD; $i++): ?>
            <a href="<?= BASE_URL ?>/oeuvre/liste?pageFilms=<?= $pageFilms ?>&pageBD=<?= $i ?>" class="<?= $i === $pageBD ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
</div>

<!-- Lien retour -->
<div class="retour-accueil">
  <a href="<?= BASE_URL ?>/">← Revenir à l’accueil</a>
</div>

</div>

<script src="<?= BASE_URL ?>/public/js/listeOeuvres.js"></script>

<?php require_once ROOT . '/app/views/templates/footer.php'; ?>
