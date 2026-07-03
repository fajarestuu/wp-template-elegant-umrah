/**
 * Packages Filter JavaScript
 * AJAX-based category filtering for package archive
 *
 * @package ElegantUmrah
 */

(function () {
    'use strict';

    const filterBtns    = document.querySelectorAll('.eu-filter-btn[data-filter]');
    const packagesGrid  = document.querySelector('.eu-packages-grid, .eu-packages-loop');

    if (!filterBtns.length || !packagesGrid) return;

    let currentFilter = 'all';
    let isFiltering   = false;

    filterBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (isFiltering || this.getAttribute('data-filter') === currentFilter) return;

            currentFilter = this.getAttribute('data-filter');

            // Update active state
            filterBtns.forEach(function (b) { b.classList.remove('active'); });
            this.classList.add('active');

            filterPackages(currentFilter);
        });
    });

    function filterPackages(filter) {
        isFiltering = true;

        // Show loading state
        packagesGrid.style.opacity = '0.5';
        packagesGrid.style.transition = 'opacity 0.3s';

        if (filter === 'all') {
            // Show all
            const allCards = packagesGrid.querySelectorAll('[data-category], .eu-package-card');
            allCards.forEach(function (card) {
                card.style.display = '';
                setTimeout(function () {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 50);
            });
            packagesGrid.style.opacity = '1';
            isFiltering = false;
            return;
        }

        // Filter by category
        const allCards = packagesGrid.querySelectorAll('[data-category]');

        if (allCards.length) {
            // Client-side filtering
            allCards.forEach(function (card) {
                const cardCat = card.getAttribute('data-category');
                if (cardCat === filter) {
                    card.style.display = '';
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(10px)';
                    setTimeout(function () {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                        card.style.transition = 'opacity 0.3s, transform 0.3s';
                    }, 50);
                } else {
                    card.style.opacity = '0';
                    setTimeout(function () {
                        card.style.display = 'none';
                    }, 300);
                }
            });

            packagesGrid.style.opacity = '1';
            isFiltering = false;
        } else {
            // AJAX filtering via WP Query
            const formData = new FormData();
            formData.append('action', 'eu_filter_packages');
            formData.append('nonce', euTheme.nonce);
            formData.append('package_type', filter);

            fetch(euTheme.ajaxUrl, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
            })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (data.success) {
                    packagesGrid.innerHTML = data.data.html;
                    packagesGrid.style.opacity = '1';

                    // Re-trigger animations
                    const newCards = packagesGrid.querySelectorAll('.eu-package-card');
                    newCards.forEach(function (card, i) {
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(20px)';
                        setTimeout(function () {
                            card.style.transition = 'opacity 0.4s, transform 0.4s';
                            card.style.opacity = '1';
                            card.style.transform = 'translateY(0)';
                        }, i * 80);
                    });
                }
                isFiltering = false;
            })
            .catch(function () {
                packagesGrid.style.opacity = '1';
                isFiltering = false;
            });
        }
    }

})();
