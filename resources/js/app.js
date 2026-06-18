import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    initMobileNav();
    initHeroSlider();
    initBackToTop();
});

function initMobileNav() {
    const toggle = document.getElementById('mobile-nav-toggle');
    const menu = document.getElementById('mobile-nav-menu');

    if (!toggle || !menu) {
        return;
    }

    toggle.addEventListener('click', () => {
        menu.classList.toggle('hidden');
        toggle.setAttribute('aria-expanded', String(!menu.classList.contains('hidden')));
    });
}

function initHeroSlider() {
    const slider = document.getElementById('hero-slider');

    if (!slider) {
        return;
    }

    const slides = slider.querySelectorAll('[data-slide]');
    const contents = slider.querySelectorAll('[data-slide-content]');
    const dots = slider.querySelectorAll('[data-dot]');
    const prevBtn = slider.querySelector('[data-prev]');
    const nextBtn = slider.querySelector('[data-next]');
    let current = 0;
    let interval;

    const show = (index) => {
        current = (index + slides.length) % slides.length;

        slides.forEach((slide, i) => {
            slide.classList.toggle('opacity-100', i === current);
            slide.classList.toggle('opacity-0', i !== current);
            slide.classList.toggle('z-10', i === current);
        });

        dots.forEach((dot, i) => {
            dot.classList.toggle('bg-primary', i === current);
            dot.classList.toggle('bg-white/50', i !== current);
        });

        contents.forEach((content, i) => {
            content.classList.toggle('hidden', i !== current);
        });
    };

    const startAuto = () => {
        clearInterval(interval);
        interval = setInterval(() => show(current + 1), 6000);
    };

    prevBtn?.addEventListener('click', () => {
        show(current - 1);
        startAuto();
    });

    nextBtn?.addEventListener('click', () => {
        show(current + 1);
        startAuto();
    });

    dots.forEach((dot, i) => {
        dot.addEventListener('click', () => {
            show(i);
            startAuto();
        });
    });

    show(0);
    startAuto();
}

function initBackToTop() {
    const button = document.getElementById('back-to-top');

    if (!button) {
        return;
    }

    window.addEventListener('scroll', () => {
        button.classList.toggle('opacity-0', window.scrollY < 400);
        button.classList.toggle('pointer-events-none', window.scrollY < 400);
        button.classList.toggle('opacity-100', window.scrollY >= 400);
    });

    button.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}
