import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    initMobileNav();
    initHeroSlider();
    initBackToTop();
    initScrollReveal();
    initCountUp();
    initProgressBars();
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
            const isActive = i === current;
            slide.classList.toggle('opacity-100', isActive);
            slide.classList.toggle('opacity-0', !isActive);
            slide.classList.toggle('z-10', isActive);

            const img = slide.querySelector('.hero-slide-img');
            if (img) {
                img.classList.toggle('is-active', isActive);
                if (isActive) {
                    img.style.animation = 'none';
                    // eslint-disable-next-line no-unused-expressions
                    img.offsetHeight;
                    img.style.animation = '';
                }
            }
        });

        dots.forEach((dot, i) => {
            dot.classList.toggle('bg-primary', i === current);
            dot.classList.toggle('scale-125', i === current);
            dot.classList.toggle('bg-white/50', i !== current);
        });

        contents.forEach((content, i) => {
            if (i === current) {
                content.classList.remove('hidden');
                content.classList.add('hero-content-enter');
            } else {
                content.classList.add('hidden');
                content.classList.remove('hero-content-enter');
            }
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
        const visible = window.scrollY >= 400;
        button.classList.toggle('opacity-0', !visible);
        button.classList.toggle('pointer-events-none', !visible);
        button.classList.toggle('opacity-100', visible);
        button.classList.toggle('translate-y-0', visible);
        button.classList.toggle('translate-y-4', !visible);
    });

    button.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

function initScrollReveal() {
    const elements = document.querySelectorAll('.reveal');

    if (!elements.length) {
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.15, rootMargin: '0px 0px -40px 0px' },
    );

    elements.forEach((el) => observer.observe(el));
}

function initCountUp() {
    const counters = document.querySelectorAll('[data-count]');

    if (!counters.length) {
        return;
    }

    const animate = (el) => {
        const target = parseFloat(el.dataset.count);
        const suffix = el.dataset.suffix || '';
        const prefix = el.dataset.prefix || '';
        const duration = 1800;
        const start = performance.now();

        const step = (now) => {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - (1 - progress) ** 3;
            const value = Math.floor(eased * target);

            el.textContent = `${prefix}${value.toLocaleString()}${suffix}`;

            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                el.textContent = `${prefix}${target.toLocaleString()}${suffix}`;
            }
        };

        requestAnimationFrame(step);
    };

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    animate(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.5 },
    );

    counters.forEach((el) => observer.observe(el));
}

function initProgressBars() {
    const bars = document.querySelectorAll('.progress-fill[data-progress]');

    if (!bars.length) {
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const bar = entry.target;
                    bar.style.setProperty('--progress', `${bar.dataset.progress}%`);
                    bar.classList.add('is-animated');
                    observer.unobserve(bar);
                }
            });
        },
        { threshold: 0.3 },
    );

    bars.forEach((bar) => observer.observe(bar));
}
