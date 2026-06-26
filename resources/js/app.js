import './bootstrap';
import Swiper from 'swiper';
import { Autoplay, Navigation, Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

document.addEventListener('DOMContentLoaded', () => {
    initMobileNav();
    initNavDropdown();
    initHeroSlider();
    initPartnersSlider();
    initBackToTop();
    initScrollReveal();
    initCountUp();
    initProgressBars();
});

function initMobileNav() {
    const toggle = document.getElementById('mobile-nav-toggle');
    const menu = document.getElementById('mobile-nav-menu');
    const iconOpen = document.getElementById('mobile-nav-icon-open');
    const iconClose = document.getElementById('mobile-nav-icon-close');
    const links = menu?.querySelectorAll('[data-mobile-nav-link]');

    if (!toggle || !menu) {
        return;
    }

    const setOpen = (open) => {
        menu.classList.toggle('is-open', open);
        menu.setAttribute('aria-hidden', String(!open));
        toggle.setAttribute('aria-expanded', String(open));
        iconOpen?.classList.toggle('hidden', open);
        iconClose?.classList.toggle('hidden', !open);
        document.body.classList.toggle('overflow-hidden', open);
    };

    toggle.addEventListener('click', () => {
        setOpen(!menu.classList.contains('is-open'));
    });

    links?.forEach((link) => {
        link.addEventListener('click', () => setOpen(false));
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1280) {
            setOpen(false);
        }
    });
}

function initNavDropdown() {
    const dropdowns = document.querySelectorAll('[data-nav-dropdown]');

    dropdowns.forEach((dropdown) => {
        const trigger = dropdown.querySelector('[data-nav-dropdown-trigger]');

        if (!trigger) {
            return;
        }

        const setOpen = (open) => {
            dropdown.classList.toggle('is-open', open);
            trigger.setAttribute('aria-expanded', String(open));
        };

        trigger.addEventListener('click', (event) => {
            event.stopPropagation();
            setOpen(!dropdown.classList.contains('is-open'));
        });
    });

    document.addEventListener('click', (event) => {
        dropdowns.forEach((dropdown) => {
            if (!dropdown.contains(event.target)) {
                dropdown.classList.remove('is-open');
                dropdown.querySelector('[data-nav-dropdown-trigger]')?.setAttribute('aria-expanded', 'false');
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') {
            return;
        }

        dropdowns.forEach((dropdown) => {
            dropdown.classList.remove('is-open');
            dropdown.querySelector('[data-nav-dropdown-trigger]')?.setAttribute('aria-expanded', 'false');
        });
    });

    document.querySelectorAll('[data-mobile-nav-dropdown]').forEach((dropdown) => {
        const toggle = dropdown.querySelector('[data-mobile-nav-dropdown-toggle]');
        const submenu = dropdown.querySelector('.mobile-nav-submenu');

        if (!toggle || !submenu) {
            return;
        }

        toggle.addEventListener('click', () => {
            const open = submenu.hasAttribute('hidden');
            submenu.toggleAttribute('hidden', !open);
            dropdown.classList.toggle('is-open', open);
            toggle.setAttribute('aria-expanded', String(open));
        });
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

function initPartnersSlider() {
    const slider = document.querySelector('.partners-swiper');

    if (!slider) {
        return;
    }

    const slideCount = slider.querySelectorAll('.swiper-slide').length;

    new Swiper(slider, {
        modules: [Navigation, Pagination, Autoplay],
        slidesPerView: 2,
        spaceBetween: 24,
        loop: slideCount > 6,
        speed: 600,
        autoplay: {
            delay: 3500,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
        },
        navigation: {
            nextEl: '.partners-swiper-next',
            prevEl: '.partners-swiper-prev',
        },
        pagination: {
            el: '.partners-swiper-pagination',
            clickable: true,
        },
        breakpoints: {
            480: { slidesPerView: 3, spaceBetween: 28 },
            768: { slidesPerView: 4, spaceBetween: 32 },
            1024: { slidesPerView: 5, spaceBetween: 36 },
            1280: { slidesPerView: 6, spaceBetween: 40 },
        },
    });
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
