<!-- app/views/accueil/indexView.php -->

<?php require_once ROOT . '/app/views/templates/header.php'; ?>

<main class="accueil">

  <!-- Fil d'Ariane -->
  <div class="fil-ariane">
    <span>ACCUEIL</span>
  </div>

  <section class="carousel">
    <button class="prev">&#9664;</button>

    <div class="carousel-inner">
      <div class="slide active">
        <img src="<?= BASE_URL ?>/public/images/femme-sauvage.png" alt="Slide 1">
      </div>
      <div class="slide">
        <img src="<?= BASE_URL ?>/public/images/robot-love.png" alt="Slide 2">
      </div>
      <div class="slide">
        <img src="<?= BASE_URL ?>/public/images/robot-mousse.png" alt="Slide 3">
      </div>
    </div>

    <button class="next">&#9654;</button>

    <div class="dots">
      <span class="dot active"></span>
      <span class="dot"></span>
      <span class="dot"></span>
    </div>
  </section>




  <!-- Articles récents + suggestions -->
  <div class="contenu-accueil">

    <!-- Articles récents -->
    <section class="articles-recents">
      <div class="navigation-articles">
        <button id="prev-article">Précédent</button>
        <h3>Article</h3>
        <button id="next-article">Suivant</button>
      </div>

      <div id="slider-articles">
        <?php foreach ($articlesRecents as $index => $article): ?>
          <article class="carte-article" style="display: <?= $index === 0 ? 'block' : 'none' ?>;">
            <?php if ($article['image']): ?>
              <img
                src="<?= BASE_URL ?>/public/upload/<?= htmlspecialchars($article['image']) ?>"
                alt="<?= htmlspecialchars($article['titre']) ?>"
                class="img-article-slider">
            <?php endif; ?>
            <h4><?= htmlspecialchars($article['titre']) ?></h4>
            <p><?= htmlspecialchars(mb_strimwidth(strip_tags($article['contenu']), 0, 120, '...')) ?></p>
            <a href="<?= BASE_URL ?>/article/fiche/<?= $article['id_article'] ?>">Lire l’article</a>
          </article>
        <?php endforeach; ?>
      </div>
    </section>



    <aside class="suggestions">
  <h4>Suggestions</h4>
  <?php foreach ($suggestions as $s) : ?>
    <div class="carte-suggestion">
      <?php
        $imagePath = ($typeSuggestions === 'article')
          ? BASE_URL . '/public/upload/' . htmlspecialchars($s['image'])
          : (filter_var($s['media'], FILTER_VALIDATE_URL)
              ? htmlspecialchars($s['media'])
              : BASE_URL . '/public/upload/' . htmlspecialchars($s['media']));

        $contenu = $typeSuggestions === 'article'
          ? strip_tags($s['contenu'])
          : strip_tags($s['analyse']);

        $extrait = mb_strimwidth($contenu, 0, 150, '...');
      ?>

      <img src="<?= $imagePath ?>" alt="Miniature" class="img-suggestion">
      <a href="<?= BASE_URL ?>/<?= $typeSuggestions ?>/fiche/<?= (int) $s['id_' . $typeSuggestions] ?>">
        <?= htmlspecialchars($s['titre']) ?>
      </a>
      <p><?= htmlspecialchars($extrait) ?></p>
    </div>
  <?php endforeach; ?>
</aside>






  </div>
</main>

<script src="<?= BASE_URL ?>/public/js/sliderArticles.js" defer></script>
<script src="<?= BASE_URL ?>/public/js/slider.js" defer></script>

<?php require_once ROOT . '/app/views/templates/footer.php'; ?>