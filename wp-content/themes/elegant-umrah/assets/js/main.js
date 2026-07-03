/**
 * Elegant Umrah - Main JavaScript
 *
 * Handles: mobile menu, header scroll, scroll-to-top,
 *          animation observer, year update
 *
 * @package ElegantUmrah
 */

(function () {
    'use strict';

    // =========================================
    // MOBILE NAVIGATION
    // =========================================
    const menuToggle = document.getElementById('eu-menu-toggle');
    const mobileNav  = document.getElementById('eu-mobile-nav');

    if (menuToggle && mobileNav) {
        menuToggle.addEventListener('click', function () {
            const isOpen = mobileNav.classList.contains('open');

            if (isOpen) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        });

        // Close on escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && mobileNav.classList.contains('open')) {
                closeMobileMenu();
                menuToggle.focus();
            }
        });

        // Close when clicking outside
        document.addEventListener('click', function (e) {
            if (mobileNav.classList.contains('open') &&
                !mobileNav.contains(e.target) &&
                !menuToggle.contains(e.target)) {
                closeMobileMenu();
            }
        });

        // Mobile sub-menu toggle
        const menuItemsWithChildren = mobileNav.querySelectorAll('.menu-item-has-children > a');
        menuItemsWithChildren.forEach(function (link) {
            link.addEventListener('click', function (e) {
                const subMenu = this.nextElementSibling;
                if (subMenu && subMenu.classList.contains('sub-menu')) {
                    e.preventDefault();
                    subMenu.style.display = subMenu.style.display === 'block' ? 'none' : 'block';
                }
            });
        });
    }

    function openMobileMenu() {
        mobileNav.classList.add('open');
        menuToggle.classList.add('active');
        menuToggle.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileMenu() {
        mobileNav.classList.remove('open');
        menuToggle.classList.remove('active');
        menuToggle.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    }

    // =========================================
    // HEADER SCROLL EFFECT
    // =========================================
    const siteHeader = document.querySelector('.eu-site-header');
    let lastScrollY = 0;

    if (siteHeader) {
        const handleHeaderScroll = throttle(function () {
            const scrollY = window.scrollY || window.pageYOffset;

            if (scrollY > 50) {
                siteHeader.classList.add('scrolled');
            } else {
                siteHeader.classList.remove('scrolled');
            }

            lastScrollY = scrollY;
        }, 100);

        window.addEventListener('scroll', handleHeaderScroll, { passive: true });
    }

    // =========================================
    // SCROLL TO TOP BUTTON
    // =========================================
    const scrollTopBtn = document.getElementById('eu-scroll-top');

    if (scrollTopBtn) {
        const handleScrollTopVisibility = throttle(function () {
            const scrollY = window.scrollY || window.pageYOffset;

            if (scrollY > 400) {
                scrollTopBtn.classList.add('visible');
            } else {
                scrollTopBtn.classList.remove('visible');
            }
        }, 150);

        window.addEventListener('scroll', handleScrollTopVisibility, { passive: true });

        scrollTopBtn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // =========================================
    // SCROLL-TRIGGERED ANIMATIONS
    // =========================================
    const animatedEls = document.querySelectorAll(
        '.eu-package-card, .eu-feature-item, .eu-testimonial-card, .eu-blog-card, .eu-stat-item, .eu-timeline-item'
    );

    if (animatedEls.length && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.style.animationDelay = (Math.random() * 0.15).toFixed(2) + 's';
                    entry.target.classList.add('eu-animate-in');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -40px 0px',
        });

        animatedEls.forEach(function (el) {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            observer.observe(el);
        });
    }

    // =========================================
    // UPDATE COPYRIGHT YEAR
    // =========================================
    const yearEl = document.getElementById('eu-year');
    if (yearEl) {
        yearEl.textContent = new Date().getFullYear();
    }

    // =========================================
    // SMOOTH SCROLL FOR ANCHOR LINKS
    // =========================================
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href === '#') return;

            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                const headerHeight = siteHeader ? siteHeader.offsetHeight : 70;
                const offsetTop = target.getBoundingClientRect().top + window.scrollY - headerHeight - 20;

                window.scrollTo({ top: offsetTop, behavior: 'smooth' });
            }
        });
    });

    // =========================================
    // STATS COUNTER ANIMATION
    // =========================================
    const statNumbers = document.querySelectorAll('.eu-stat-item .number');

    if (statNumbers.length && 'IntersectionObserver' in window) {
        const counterObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    counterObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        statNumbers.forEach(function (el) {
            counterObserver.observe(el);
        });
    }

    function animateCounter(el) {
        const text = el.textContent.trim();
        const match = text.match(/^(\d[\d,]*)(\+?[^0-9]*)$/);
        if (!match) return;

        const target    = parseInt(match[1].replace(/,/g, ''), 10);
        const suffix    = match[2] || '';
        const duration  = 1500;
        const step      = Math.ceil(duration / 60);
        const increment = target / (duration / step);
        let current     = 0;

        const timer = setInterval(function () {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            el.textContent = formatNumber(Math.floor(current)) + suffix;
        }, step);
    }

    function formatNumber(n) {
        return n >= 1000 ? (n / 1000).toFixed(0) + 'K' : n.toString();
    }

    // =========================================
    // CATEGORY FILTER (Archive / Packages)
    // =========================================
    const filterBtns = document.querySelectorAll('.eu-filter-btn');

    if (filterBtns.length) {
        filterBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                // Remove active from all
                filterBtns.forEach(function (b) { b.classList.remove('active'); });
                this.classList.add('active');

                const filter = this.getAttribute('data-filter');
                const cards  = document.querySelectorAll('[data-category]');

                cards.forEach(function (card) {
                    if (filter === 'all' || card.getAttribute('data-category') === filter) {
                        card.style.display = '';
                        setTimeout(function () { card.style.opacity = '1'; }, 50);
                    } else {
                        card.style.opacity = '0';
                        setTimeout(function () { card.style.display = 'none'; }, 300);
                    }
                });
            });
        });
    }

    // =========================================
    // UTILITY: THROTTLE
    // =========================================
    function throttle(fn, delay) {
        let lastCall = 0;
        return function (...args) {
            const now = Date.now();
            if (now - lastCall >= delay) {
                lastCall = now;
                return fn.apply(this, args);
            }
        };
    }

    // =========================================
    // TOUCH SWIPE for mobile (packages)
    // =========================================
    let touchStartX = 0;
    let touchEndX   = 0;

    document.addEventListener('touchstart', function (e) {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });

    document.addEventListener('touchend', function (e) {
        touchEndX = e.changedTouches[0].screenX;
    }, { passive: true });

    // =========================================
    // INIT on DOMContentLoaded
    // =========================================
    document.addEventListener('DOMContentLoaded', function () {
        // Mark body as JS-ready
        document.documentElement.classList.add('js-ready');
    });

})();
