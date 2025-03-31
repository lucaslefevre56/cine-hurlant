<!-- app/views/oeuvres/listeOeuvresView.php -->
 
<?php require_once ROOT . '/app/views/templates/header.php'; ?>

<!-- Titre principal de la page -->
<h2>Les Œuvres</h2>

<!-- Je vérifie qu’il y a bien des œuvres à afficher -->
<?php if (!empty($oeuvres)) : ?>

    <!-- J’affiche un conteneur pour toutes les cartes d’œuvres -->
    <div class="liste-oeuvres">

        <!-- Je parcours chaque œuvre pour créer une carte individuelle -->
        <?php foreach ($oeuvres as $oeuvre) : ?>
            <div class="carte-oeuvre">

                <!-- Je montre le titre de l’œuvre -->
                <h3><?= htmlspecialchars($oeuvre['titre']) ?></h3>

                <!-- Je précise l’auteur de l’œuvre -->
                <p><strong>Auteur :</strong> <?= htmlspecialchars($oeuvre['auteur']) ?></p>

                <!-- Je précise le type (film ou BD) -->
                <p><strong>Type :</strong> <?= htmlspecialchars($oeuvre['nom']) ?></p>

                <!-- Je vérifie si l’œuvre a des genres associés -->
                <?php if (!empty($oeuvre['genres'])) : ?>
                    <p><strong>Genres :</strong> 
                        <!-- Je sécurise chaque genre et je les affiche séparés par des virgules -->
                        <?= implode(', ', array_map('htmlspecialchars', $oeuvre['genres'])) ?>
                    </p>
                <?php else : ?>
                    <p><em>Aucun genre associé</em></p>
                <?php endif; ?>

                <!-- J’affiche l’année de publication -->
                <p><strong>Année :</strong> <?= htmlspecialchars($oeuvre['annee']) ?></p>

                <!-- Si un média est présent (image ou autre), je l’affiche uniquement lorsqu'elle entre dans la zone visible de l'ecran avec lazy loading -->
                <?php if (!empty($oeuvre['media'])) : ?>
                    <img src="<?= htmlspecialchars($oeuvre['media']) ?>" alt="Visuel de <?= htmlspecialchars($oeuvre['titre']) ?>" width="200" loading="lazy">
                <?php endif; ?>

                <!-- Je montre un extrait de l’analyse (juste les 150 premiers caractères) -->
                <p><strong>Analyse :</strong> <?= htmlspecialchars(substr($oeuvre['analyse'], 0, 150)) ?>...</p>

                <!-- Je propose un lien vers la fiche complète de l’œuvre -->
                <a href="/cine-hurlant/public/oeuvre/fiche/<?= $oeuvre['id_oeuvre'] ?>">Voir la fiche complète</a>

            </div>
        <?php endforeach; ?>
    </div>

<!-- Si aucune œuvre n’est présente, j’affiche un message d’attente -->
<?php else : ?>
    <p>Aucune œuvre enregistrée pour le moment</p>
<?php endif; ?>

<?php require_once ROOT . '/app/views/templates/footer.php'; ?>
