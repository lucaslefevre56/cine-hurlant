</main> <!-- 🆕 Fermeture ici -->

<footer style="margin-top: 2rem; text-align: center;">
    <p>&copy; 2025 - Ciné-Hurlant</p>
</footer>

<!-- Bandeau cookie RGPD -->
<div id="cookie-banner" style="display: none; background: #f2f2f2; padding: 15px; border-top: 1px solid #ccc; position: fixed; bottom: 0; width: 100%; z-index: 9999; text-align: center;">
    <span>Ce site utilise des cookies pour améliorer votre expérience.</span>
    <button onclick="acceptCookies()" style="margin-left: 10px;">Accepter</button>
    <button onclick="refuseCookies()" style="margin-left: 5px;">Refuser</button>
</div>

<!-- JS personnalisés -->
<?php if (!empty($loadCommentairesJs)) : ?>
  <script src="<?= BASE_URL ?>/public/js/commentaires.js"></script>
<?php endif; ?>

<script src="<?= BASE_URL ?>/public/js/script.js"></script>
<script src="<?= BASE_URL ?>/public/js/cookieConsentement.js"></script>

<!-- Variables utilisateur JS -->
<?php if (isset($_SESSION['user'])): ?>
  <script>
    const userId = <?= (int) $_SESSION['user']['id'] ?>;
    const userRole = "<?= htmlspecialchars($_SESSION['user']['role']) ?>";
  </script>
<?php else: ?>
  <script>
    const userId = null;
    const userRole = "";
  </script>
<?php endif; ?>

</body>
</html>
