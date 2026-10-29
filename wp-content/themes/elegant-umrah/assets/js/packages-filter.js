/**
 * Packages Filter JavaScript
 * AJAX-based category filtering for package archive
 *
 * @package ElegantUmrah
 */

(function () {
    'use strict';

    const filterBtns   = document.querySelectorAll('.eu-filter-btn[data-filter]');
    const packagesGrid = document.querySelector('.eu-packages-grid, .eu-packages-loop');

    if (!filterBtns.length || !packagesGrid) return;

    const activeBtn   = document.querySelector('.eu-filter-btn.active[data-filter]');
    let currentFilter = activeBtn ? activeBtn.getAttribute('data-filter') : 'all';
    let isFiltering   = false;

    filterBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const selectedFilter = this.getAttribute('data-filter') || 'all';
            if (isFiltering || selectedFilter === currentFilter) return;

            currentFilter = selectedFilter;

            // Update active state
            filterBtns.forEach(function (b) { b.classList.remove('active'); });
            this.classList.add('active');

            // Update URL query param without reloading page
            if (window.history && window.history.replaceState) {
                const url = new URL(window.location.href);
                if (currentFilter === 'all') {
                    url.searchParams.delete('type');
                } else {
                    url.searchParams.set('type', currentFilter);
                }
                window.history.replaceState({}, '', url.toString());
            }

            filterPackages(currentFilter);
        });
    });

    function filterPackages(filter) {
        isFiltering = true;

        // Show loading state
        packagesGrid.style.opacity = '0.5';
        packagesGrid.style.transition = 'opacity 0.3s';

        // Prefer server-side AJAX filtering so packages across all pages are included
        if (typeof euTheme !== 'undefined' && euTheme.ajaxUrl) {
            const formData = new FormData();
            formData.append('action', 'eu_filter_packages');
            formData.append('nonce', euTheme.nonce);
            formData.append('package_type', filter);

            // Pass active month filter if present in URL
            const urlParams = new URLSearchParams(window.location.search);
            const activeMonth = urlParams.get('month');
            if (activeMonth) {
                formData.append('month', activeMonth);
            }

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
            return;
        }

        // Fallback: Client-side filtering when AJAX config is unavailable
        const allCards = packagesGrid.querySelectorAll('[data-category]');
        allCards.forEach(function (card) {
            const cardCat = card.getAttribute('data-category');
            if (filter === 'all' || cardCat === filter) {
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
    }

})();
