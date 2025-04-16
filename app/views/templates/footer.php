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

<!-- Modal de confirmation "Annuler" -->
<div id="annuler-confirm-modal" class="modal-overlay" style="display: none;">
  <div class="modal-box">
    <p>Les modifications non enregistrées seront perdues.<br>Voulez-vous vraiment annuler ?</p>
    <div class="modal-actions">
      <button id="confirm-annuler" class="btn-danger">Oui, annuler</button>
      <button id="cancel-annuler" class="btn-secondary">Continuer</button>
    </div>
  </div>
</div>

<!-- Modal de confirmation suppression -->
<div id="supprimer-confirm-modal" class="modal-overlay" style="display: none;">
  <div class="modal-box">
    <p>Cette action est irréversible. <br>Voulez-vous vraiment supprimer cet élément ?</p>
    <div class="modal-actions">
      <button id="confirm-supprimer" class="btn-danger">Oui, supprimer</button>
      <button id="cancel-supprimer" class="btn-secondary">Annuler</button>
    </div>
  </div>
</div>

<!-- Modal de confirmation suppression commentaire -->
<div id="commentaire-confirm-modal" class="modal-overlay" style="display: none;">
  <div class="modal-box">
    <p>Supprimer ce commentaire ?</p>
    <div class="modal-actions">
      <button id="confirm-commentaire-suppression" class="btn-danger">Oui, supprimer</button>
      <button id="cancel-commentaire-suppression" class="btn-secondary">Annuler</button>
    </div>
  </div>
</div>

<!-- Modal de confirmation désactivation de compte -->
<div id="desactivation-confirm-modal" class="modal-overlay" style="display: none;">
  <div class="modal-box">
    <p>Le compte sera désactivé et l’utilisateur ne pourra plus se connecter.<br>Voulez-vous vraiment désactiver ce compte ?</p>
    <div class="modal-actions">
      <button id="confirm-desactivation" class="btn-danger">Oui, désactiver</button>
      <button id="cancel-desactivation" class="btn-secondary">Annuler</button>
    </div>
  </div>
</div>

</body>

</html>