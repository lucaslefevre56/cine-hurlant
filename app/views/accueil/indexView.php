<!-- app/views/accueil/indexView.php -->

<?php require_once ROOT . '/app/views/templates/header.php'; ?>

<?php if (!empty($_SESSION['message_suppression'])) : ?>
  <div class="message-success" id="message-succes">
    <?= htmlspecialchars($_SESSION['message_suppression']) ?>
  </div>
  <?php unset($_SESSION['message_suppression']); ?>
<?php endif; ?>

<main class="accueil">

  <h1>Ciné-Hurlant</h1>
  <h2>analyses croisées entre cinéma et BD de science-fiction</h2>

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

  <!-- Contenu principal avec présentation + articles + suggestions -->
  <div class="contenu-accueil">

    <div class="bloc-gauche">

      <!-- Présentation -->
      <section class="presentation-site">
        <h3>Bienvenue dans l’univers de Ciné-Hurlant</h3>
        <p>
          Un espace de découverte et d’analyse dédié aux passionnés de science-fiction.
          Ici, les frontières s’estompent entre grand écran et planches illustrées, entre mythes futuristes et réflexions contemporaines.
        </p>
        <h4>Un regard croisé, critique et curieux</h4>
        <p>
          Ce site propose des <strong>analyses croisées</strong> entre œuvres cinématographiques et bandes dessinées,
          pour mieux comprendre comment elles se répondent, s’inspirent et se transforment au fil des époques.
        </p>
        <p>
          Que vous soyez amateur de récits dystopiques, de mondes post-apocalyptiques, de robots rêveurs ou de civilisations interstellaires,
          <strong>Ciné•Hurlant</strong> vous invite à explorer des œuvres cultes et méconnues à travers des articles fouillés,
          des suggestions inattendues et un regard critique nourri par la passion.
        </p>
        <p><em>Bonne exploration, et que la fiction résonne !</em></p>
      </section>

      <!-- Articles récents -->
      <section class="articles-recents">
        <div class="navigation-articles">
          <button id="prev-article">Précédent</button>
          <h3>Articles Récents</h3>
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

    </div>

    <!-- Suggestions -->
    <aside class="suggestions">
      <h4>Suggestions de l'équipe</h4>
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
