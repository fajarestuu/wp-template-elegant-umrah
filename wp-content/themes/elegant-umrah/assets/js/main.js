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
    // INIT on DOMContentLoaded
    // =========================================
    document.addEventListener('DOMContentLoaded', function () {
        // Mark body as JS-ready
        document.documentElement.classList.add('js-ready');
    });

    // =========================================
    // READING PROGRESS BAR (Single Post)
    // =========================================
    const readProgress = document.getElementById('eu-read-progress');

    if (readProgress) {
        const updateProgress = throttle(function () {
            const scrollTop    = window.scrollY || window.pageYOffset;
            const docHeight    = document.documentElement.scrollHeight - window.innerHeight;
            const pct          = docHeight > 0 ? Math.min(100, (scrollTop / docHeight) * 100) : 0;
            readProgress.style.width = pct.toFixed(1) + '%';
        }, 50);

        window.addEventListener('scroll', updateProgress, { passive: true });
    }

    // =========================================
    // ARTICLE CATEGORY FILTER (AJAX)
    // =========================================
    const articleFilterBtns = document.querySelectorAll('.eu-article-filter-btn');
    const articleGrid        = document.getElementById('eu-article-grid');
    const articlesLoading    = document.getElementById('eu-articles-loading');
    const loadMoreBtn        = document.getElementById('eu-articles-load-more');
    const articlesContainer  = document.getElementById('eu-articles-container');

    if (articleFilterBtns.length && articleGrid && typeof euArticles !== 'undefined') {

        let currentCategory = 'all';
        let currentPage     = 1;
        let maxPages        = 1;
        let isFetching      = false;

        // Detect total pages from initial load (set via data attr if available)
        if (articlesContainer) {
            maxPages = parseInt(articlesContainer.dataset.maxPages || '1', 10);
        }

        // Show/hide load-more button
        function updateLoadMore() {
            if (!loadMoreBtn) return;
            if (currentPage < maxPages) {
                loadMoreBtn.style.display = 'inline-flex';
                loadMoreBtn.dataset.paged = currentPage + 1;
            } else {
                loadMoreBtn.style.display = 'none';
            }
        }

        // Fetch articles via AJAX
        function fetchArticles(category, page, append) {
            if (isFetching) return;
            isFetching = true;

            if (articlesLoading) articlesLoading.style.display = 'flex';
            if (articleGrid)     articleGrid.style.opacity = append ? '1' : '0.4';

            const formData = new FormData();
            formData.append('action',   euArticles.action);
            formData.append('nonce',    euArticles.nonce);
            formData.append('category', category);
            formData.append('paged',    page);

            fetch(euArticles.ajaxUrl, {
                method: 'POST',
                body:   formData,
                credentials: 'same-origin',
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (!data.success) throw new Error(euArticles.i18n.error);

                if (append) {
                    articleGrid.insertAdjacentHTML('beforeend', data.data.html);
                } else {
                    articleGrid.innerHTML = data.data.html;
                    // scroll to grid smoothly
                    const offset = (articleGrid.getBoundingClientRect().top + window.scrollY) - 140;
                    window.scrollTo({ top: Math.max(0, offset), behavior: 'smooth' });
                }

                maxPages = data.data.max_pages || 1;
                currentPage = data.data.paged || page;
                updateLoadMore();

                // Re-observe new cards for animation
                const newCards = articleGrid.querySelectorAll('.eu-article-card:not(.eu-animate-in)');
                if ('IntersectionObserver' in window) {
                    const obs = new IntersectionObserver(function (entries) {
                        entries.forEach(function (entry) {
                            if (entry.isIntersecting) {
                                entry.target.classList.add('eu-animate-in');
                                obs.unobserve(entry.target);
                            }
                        });
                    }, { threshold: 0.08 });
                    newCards.forEach(function (card) {
                        card.style.opacity    = '0';
                        card.style.transform  = 'translateY(16px)';
                        obs.observe(card);
                    });
                }
            })
            .catch(function (err) {
                console.error('[EU Articles]', err);
            })
            .finally(function () {
                isFetching = false;
                if (articlesLoading) articlesLoading.style.display = 'none';
                if (articleGrid)     articleGrid.style.opacity = '1';
            });
        }

        // Filter button clicks
        articleFilterBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                articleFilterBtns.forEach(function (b) {
                    b.classList.remove('active');
                    b.setAttribute('aria-selected', 'false');
                });
                this.classList.add('active');
                this.setAttribute('aria-selected', 'true');

                currentCategory = this.dataset.category || 'all';
                currentPage     = 1;

                // Hide featured card if filtering
                const featured = document.querySelector('.eu-article-featured');
                if (featured) {
                    featured.style.display = currentCategory === 'all' ? '' : 'none';
                }

                fetchArticles(currentCategory, 1, false);
            });
        });

        // Load more button click
        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', function () {
                fetchArticles(currentCategory, currentPage + 1, true);
            });
        }
    }

    // =========================================
    // COPY LINK BUTTON (Single Post)
    // =========================================
    const copyLinkBtn = document.getElementById('eu-copy-link');

    if (copyLinkBtn && navigator.clipboard) {
        copyLinkBtn.addEventListener('click', function () {
            const url = this.dataset.url || window.location.href;
            navigator.clipboard.writeText(url).then(function () {
                copyLinkBtn.style.background = '#D4AF37';
                copyLinkBtn.style.color      = '#0B0B0B';
                setTimeout(function () {
                    copyLinkBtn.style.background = '';
                    copyLinkBtn.style.color      = '';
                }, 1500);
            });
        });
    }

    // =========================================
    // HERO SEARCH FORM (CLEAN URL SUBMISSION)
    // =========================================
    const heroSearchForm = document.querySelector('.eu-hero-search-form');
    if (heroSearchForm) {
        heroSearchForm.addEventListener('submit', function () {
            const monthInput = this.querySelector('[name="month"]');
            const typeInput  = this.querySelector('[name="type"]');

            if (monthInput && !monthInput.value) {
                monthInput.disabled = true;
            }
            if (typeInput && (!typeInput.value || typeInput.value === 'all')) {
                typeInput.disabled = true;
            }
        });

        // Re-enable if user navigates back (bfcache)
        window.addEventListener('pageshow', function () {
            const inputs = heroSearchForm.querySelectorAll('select');
            inputs.forEach(function (input) {
                input.disabled = false;
            });
        });
    }

    // =========================================
    // HERO SLIDESHOW CONTROLLER
    // =========================================
    const heroMedia = document.querySelector('.eu-hero-media');
    if (heroMedia) {
        const slides = heroMedia.querySelectorAll('.eu-hero-slide');
        const dots   = heroMedia.querySelectorAll('.eu-hero-dot');
        const count  = slides.length;

        if (count > 1) {
            const intervalSec = parseInt(heroMedia.getAttribute('data-interval') || '5', 10);
            const intervalMs  = Math.max(3000, intervalSec * 1000);
            let currentIdx    = 0;
            let slideTimer    = null;

            function goToSlide(index) {
                currentIdx = (index + count) % count;
                slides.forEach(function (slide, i) {
                    if (i === currentIdx) {
                        slide.classList.add('is-active');
                    } else {
                        slide.classList.remove('is-active');
                    }
                });
                dots.forEach(function (dot, i) {
                    if (i === currentIdx) {
                        dot.classList.add('is-active');
                    } else {
                        dot.classList.remove('is-active');
                    }
                });
            }

            function startTimer() {
                stopTimer();
                slideTimer = setInterval(function () {
                    goToSlide(currentIdx + 1);
                }, intervalMs);
            }

            function stopTimer() {
                if (slideTimer) {
                    clearInterval(slideTimer);
                    slideTimer = null;
                }
            }

            // Dot click event
            dots.forEach(function (dot) {
                dot.addEventListener('click', function () {
                    const targetIdx = parseInt(this.getAttribute('data-slide-index') || '0', 10);
                    goToSlide(targetIdx);
                    startTimer();
                });
            });

            // Pause on tab hidden, resume on tab visible
            document.addEventListener('visibilitychange', function () {
                if (document.hidden) {
                    stopTimer();
                } else {
                    startTimer();
                }
            });

            // Start auto slideshow
            startTimer();
        }
    }

})();

