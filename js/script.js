// Menu Mobile
const burgerMenu = document.querySelector('.burger-menu');
const mobileMenu = document.querySelector('.mobile-menu');
const mobileMenuClose = document.querySelector('.mobile-menu-close');

burgerMenu.addEventListener('click', () => {
    mobileMenu.classList.add('active');
    document.body.style.overflow = 'hidden';
});

mobileMenuClose.addEventListener('click', () => {
    mobileMenu.classList.remove('active');
    document.body.style.overflow = '';
});

// Hero Slider
const heroSlider = {
    currentSlide: 0,
    slides: document.querySelectorAll('.hero-slide'),
    dots: document.querySelector('.hero-dots'),
    prevBtn: document.querySelector('.hero-prev'),
    nextBtn: document.querySelector('.hero-next'),
    autoplayInterval: null,

    init() {
        // Créer les points
        this.slides.forEach((_, index) => {
            const dot = document.createElement('button');
            dot.classList.add('hero-dot');
            if (index === 0) dot.classList.add('active');
            dot.setAttribute('aria-label', `Slide ${index + 1}`);
            dot.addEventListener('click', () => this.goToSlide(index));
            this.dots.appendChild(dot);
        });

        // Ajouter les événements
        this.prevBtn.addEventListener('click', () => this.prevSlide());
        this.nextBtn.addEventListener('click', () => this.nextSlide());

        // Démarrer l'autoplay
        this.startAutoplay();

        // Arrêter l'autoplay au survol
        const heroSection = document.querySelector('.hero');
        heroSection.addEventListener('mouseenter', () => this.stopAutoplay());
        heroSection.addEventListener('mouseleave', () => this.startAutoplay());
    },

    updateSlides() {
        // Mettre à jour les slides
        this.slides.forEach((slide, index) => {
            slide.classList.remove('active');
            this.dots.children[index].classList.remove('active');
        });
        this.slides[this.currentSlide].classList.add('active');
        this.dots.children[this.currentSlide].classList.add('active');
    },

    nextSlide() {
        this.currentSlide = (this.currentSlide + 1) % this.slides.length;
        this.updateSlides();
    },

    prevSlide() {
        this.currentSlide = (this.currentSlide - 1 + this.slides.length) % this.slides.length;
        this.updateSlides();
    },

    goToSlide(index) {
        this.currentSlide = index;
        this.updateSlides();
    },

    startAutoplay() {
        this.stopAutoplay();
        this.autoplayInterval = setInterval(() => this.nextSlide(), 5000);
    },

    stopAutoplay() {
        if (this.autoplayInterval) {
            clearInterval(this.autoplayInterval);
            this.autoplayInterval = null;
        }
    }
};

// Initialiser le slider quand le DOM est chargé
document.addEventListener('DOMContentLoaded', () => {
    heroSlider.init();
});