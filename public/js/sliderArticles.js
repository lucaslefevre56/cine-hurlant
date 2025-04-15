document.addEventListener("DOMContentLoaded", () => {
    const articles = document.querySelectorAll(".carte-article");
    const prevBtn = document.querySelector(".navigation-articles button:first-child");
    const nextBtn = document.querySelector(".navigation-articles button:last-child");

    let currentArticleIndex = 0;

    function showArticle(index) {
        articles.forEach((article, i) => {
            article.style.display = i === index ? "block" : "none";
        });
    }

    // Initialisation
    if (articles.length > 0) {
        showArticle(currentArticleIndex);
    }

    prevBtn?.addEventListener("click", () => {
        currentArticleIndex = (currentArticleIndex - 1 + articles.length) % articles.length;
        showArticle(currentArticleIndex);
    });

    nextBtn?.addEventListener("click", () => {
        currentArticleIndex = (currentArticleIndex + 1) % articles.length;
        showArticle(currentArticleIndex);
    });
});
