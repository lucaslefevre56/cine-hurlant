let currentSlide = 0;
const slides = document.querySelectorAll(".slide");
const dots = document.querySelectorAll(".dot");
const prevBtn = document.querySelector(".prev");
const nextBtn = document.querySelector(".next");

// Fonction de changement de slide
function showSlide(index) {
  slides.forEach((slide, i) => {
    slide.classList.toggle("active", i === index);
    dots[i].classList.toggle("active", i === index);
  });
}

// Navigation manuelle
prevBtn.addEventListener("click", () => {
  currentSlide = (currentSlide - 1 + slides.length) % slides.length;
  showSlide(currentSlide);
});

nextBtn.addEventListener("click", () => {
  currentSlide = (currentSlide + 1) % slides.length;
  showSlide(currentSlide);
});

// Navigation avec dots
dots.forEach((dot, i) => {
  dot.addEventListener("click", () => {
    currentSlide = i;
    showSlide(currentSlide);
  });
});

// Défilement automatique
setInterval(() => {
  currentSlide = (currentSlide + 1) % slides.length;
  showSlide(currentSlide);
}, 8000); // toutes les 5 secondes

// Initialisation
showSlide(currentSlide);
