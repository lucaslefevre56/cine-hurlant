// public/js/slider.js

let currentSlide = 0; // Slide actuellement affiché (par défaut le premier)

// Je récupère tous les éléments du carrousel : les slides, les points (dots), et les boutons de navigation
const slides = document.querySelectorAll(".slide");
const dots = document.querySelectorAll(".dot");
const prevBtn = document.querySelector(".prev");
const nextBtn = document.querySelector(".next");

// Fonction principale : elle affiche le slide correspondant à l’index donné
function showSlide(index) {
  slides.forEach((slide, i) => {
    // Je rends actif uniquement le slide à l’index donné
    slide.classList.toggle("active", i === index);
    // Idem pour les petits points de navigation
    dots[i].classList.toggle("active", i === index);
  });
}

// Si l’utilisateur clique sur le bouton précédent
prevBtn.addEventListener("click", () => {
  // Je décrémente l’index en bouclant à la fin si on est au début
  currentSlide = (currentSlide - 1 + slides.length) % slides.length;
  showSlide(currentSlide);
});

// Si l’utilisateur clique sur le bouton suivant
nextBtn.addEventListener("click", () => {
  // Je passe au slide suivant, avec retour au début si on dépasse la fin
  currentSlide = (currentSlide + 1) % slides.length;
  showSlide(currentSlide);
});

// Si l’utilisateur clique sur l’un des petits points sous le slider
dots.forEach((dot, i) => {
  dot.addEventListener("click", () => {
    // Je passe directement au slide correspondant au point cliqué
    currentSlide = i;
    showSlide(currentSlide);
  });
});

// Défilement automatique toutes les 8 secondes
setInterval(() => {
  currentSlide = (currentSlide + 1) % slides.length;
  showSlide(currentSlide);
}, 8000);

// J’affiche le premier slide dès le chargement de la page
showSlide(currentSlide);
