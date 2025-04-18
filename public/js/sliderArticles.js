// public/js/sliderArticles.js

document.addEventListener("DOMContentLoaded", () => {
    // Je récupère tous les éléments représentant un article à faire défiler
    const articles = document.querySelectorAll(".carte-article");

    // Je récupère les boutons de navigation (précédent et suivant)
    const prevBtn = document.querySelector(".navigation-articles button:first-child");
    const nextBtn = document.querySelector(".navigation-articles button:last-child");

    let currentArticleIndex = 0; // Index de l'article actuellement affiché

    // Fonction qui affiche un seul article à la fois (celui correspondant à l’index donné)
    function showArticle(index) {
        articles.forEach((article, i) => {
            article.style.display = i === index ? "block" : "none"; // Affiche l’article ciblé, cache les autres
        });
    }

    // Lors du chargement, j’affiche le premier article s’il y en a au moins un
    if (articles.length > 0) {
        showArticle(currentArticleIndex);
    }

    // Navigation vers l’article précédent (avec retour au dernier si on est au début)
    prevBtn?.addEventListener("click", () => {
        currentArticleIndex = (currentArticleIndex - 1 + articles.length) % articles.length;
        showArticle(currentArticleIndex);
    });

    // Navigation vers l’article suivant (avec retour au premier si on est à la fin)
    nextBtn?.addEventListener("click", () => {
        currentArticleIndex = (currentArticleIndex + 1) % articles.length;
        showArticle(currentArticleIndex);
    });
});
