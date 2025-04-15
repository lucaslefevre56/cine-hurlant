<!-- app/views/templates/footer.php -->

<footer class="site-footer">
  <div class="footer-container">

    <!-- Colonne gauche : Copyright -->
    <div class="footer-left">
      <p>&copy; 2025 ciné-hurlant</p>
    </div>

    <!-- Colonne centrale : Logo -->
    <div class="footer-center">
      <a href="<?= BASE_URL ?>/">
        <img src="<?= BASE_URL ?>/public/images/logo-footer.jpg" alt="Logo Ciné-Hurlant" class="footer-logo">
      </a>
    </div>

    <!-- Colonne droite : Mentions légales -->
    <div class="footer-right">
      <a href="<?= BASE_URL ?>/static/mentionsLegales">Mentions légales</a>
      <a href="<?= BASE_URL ?>/static/confidentialite">Politique de confidentialité</a>
    </div>

  </div>
</footer>

<!-- Bandeau cookie RGPD -->
<div id="cookie-banner" style="display: none;" class="cookie-banner">
  <span>Ce site utilise des cookies pour améliorer votre expérience.</span>
  <button onclick="acceptCookies()">Accepter</button>
  <button onclick="refuseCookies()">Refuser</button>
</div>

<!-- Variables JavaScript globales -->
<script>
  const BASE_URL = "<?= BASE_URL ?>";
  const userId = <?= isset($_SESSION['user']) ? (int) $_SESSION['user']['id'] : 'null' ?>;
  const userRole = "<?= isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']['role']) : '' ?>";
</script>

<!-- JS personnalisés -->
<?php if (!empty($loadCommentairesJs)) : ?>
  <script src="<?= BASE_URL ?>/public/js/commentaires.js"></script>
<?php endif; ?>

<script src="<?= BASE_URL ?>/public/js/recherche.js" defer></script>
<script src="<?= BASE_URL ?>/public/js/script.js"></script>
<script src="<?= BASE_URL ?>/public/js/cookieConsentement.js"></script>

</body>
</html>
