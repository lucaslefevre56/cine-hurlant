<!-- app/views/templates/footer.php -->

<footer style="margin-top: 2rem; text-align: center;">
    <p>&copy; 2025 - Ciné-Hurlant</p>
</footer>

<script src="/cine-hurlant/public/js/commentaires.js"></script>

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
