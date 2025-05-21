document.addEventListener('DOMContentLoaded', function() {
    // Gestion des filtres sur mobile
    const filtersToggle = document.querySelector('.filters-toggle');
    const filtersSection = document.querySelector('.filters-section');
    const filtersCount = document.querySelector('.filters-count');
    const btnClearFilters = document.querySelector('.btn-clear-filters');
    const btnApplyFilters = document.querySelector('.btn-apply-filters');
    let filtersOverlay;

    // Créer l'overlay pour les filtres
    function createFiltersOverlay() {
        filtersOverlay = document.createElement('div');
        filtersOverlay.className = 'filters-overlay';
        document.body.appendChild(filtersOverlay);

        // Fermer les filtres en cliquant sur l'overlay
        filtersOverlay.addEventListener('click', closeFilters);
    }

    // Ouvrir les filtres
    function openFilters() {
        filtersSection.classList.add('active');
        filtersOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    // Fermer les filtres
    function closeFilters() {
        filtersSection.classList.remove('active');
        filtersOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    // Mettre à jour le compteur de filtres
    function updateFiltersCount() {
        const activeFilters = Array.from(filterInputs).filter(input => input.checked).length;
        filtersCount.textContent = activeFilters > 0 ? activeFilters : '';
    }

    // Réinitialiser les filtres
    function clearFilters() {
        filterInputs.forEach(input => {
            input.checked = false;
        });
        updateFiltersCount();
        applyFilters();
    }

    if (filtersToggle && filtersSection) {
        createFiltersOverlay();
        filtersToggle.addEventListener('click', openFilters);

        // Gérer le bouton de fermeture
        const closeButton = document.querySelector('.filters-close');
        if (closeButton) {
            closeButton.addEventListener('click', closeFilters);
        }

        // Gérer la fermeture avec la touche Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && filtersSection.classList.contains('active')) {
                closeFilters();
            }
        });

        // Gérer les boutons d'action des filtres
        if (btnClearFilters) {
            btnClearFilters.addEventListener('click', clearFilters);
        }

        if (btnApplyFilters) {
            btnApplyFilters.addEventListener('click', () => {
                applyFilters();
                closeFilters();
            });
        }
    }

    // Gestion des favoris
    const favoriteButtons = document.querySelectorAll('.btn-favorite');
    favoriteButtons.forEach(button => {
        button.addEventListener('click', () => {
            button.classList.toggle('active');
            const icon = button.querySelector('i');
            if (button.classList.contains('active')) {
                icon.classList.remove('far');
                icon.classList.add('fas');
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
            }
        });
    });

    // Gestion de la recherche en temps réel
    const searchBar = document.querySelector('.search-bar');
    const searchSuggestions = document.querySelector('.search-suggestions');
    let searchTimeout;

    if (searchBar) {
        searchBar.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            const query = e.target.value;

            if (query.length >= 2) {
                searchTimeout = setTimeout(() => {
                    searchProducts(query);
                }, 300);
            } else {
                searchSuggestions.style.display = 'none';
            }
        });

        // Fermer les suggestions en cliquant en dehors
        document.addEventListener('click', (e) => {
            if (!searchBar.contains(e.target)) {
                searchSuggestions.style.display = 'none';
            }
        });
    }

    // Gestion des filtres
    const filterInputs = document.querySelectorAll('.filter-option input');
    filterInputs.forEach(input => {
        input.addEventListener('change', () => {
            updateFiltersCount();
            if (window.innerWidth >= 768) {
                // Appliquer automatiquement sur desktop
                applyFilters();
            }
        });
    });

    // Gestion du tri
    const sortSelect = document.querySelector('.sort-select');
    if (sortSelect) {
        sortSelect.addEventListener('change', () => {
            sortProducts(sortSelect.value);
        });
    }

    // Gestion de la pagination
    const paginationButtons = document.querySelectorAll('.pagination-btn');
    paginationButtons.forEach(button => {
        if (!button.disabled) {
            button.addEventListener('click', () => {
                paginationButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
                loadPage(button.textContent);
            });
        }
    });

    // Fonctions utilitaires
    function searchProducts(query) {
        const mockResults = [
            'Aliment Volaille Croissance',
            'Aliment Porc Engraissement',
            'Accessoires d\'élevage',
        ].filter(item => item.toLowerCase().includes(query.toLowerCase()));

        if (mockResults.length > 0) {
            searchSuggestions.innerHTML = mockResults
                .map(result => `<div class="search-suggestion">${result}</div>`)
                .join('');
            searchSuggestions.style.display = 'block';

            // Ajouter des événements de clic sur les suggestions
            const suggestions = searchSuggestions.querySelectorAll('.search-suggestion');
            suggestions.forEach(suggestion => {
                suggestion.addEventListener('click', () => {
                    searchBar.value = suggestion.textContent;
                    searchSuggestions.style.display = 'none';
                    // Déclencher la recherche
                    applyFilters();
                });
            });
        } else {
            searchSuggestions.style.display = 'none';
        }
    }

    function applyFilters() {
        const selectedFilters = {
            animals: [],
            categories: [],
            availability: []
        };

        filterInputs.forEach(input => {
            if (input.checked) {
                const filterType = input.name;
                const filterValue = input.value;
                if (selectedFilters[filterType]) {
                    selectedFilters[filterType].push(filterValue);
                }
            }
        });

        // Simuler l'application des filtres
        console.log('Filtres appliqués:', selectedFilters);
        
        // Ajouter la classe loading à la grille de produits
        const productsGrid = document.querySelector('.products-grid');
        if (productsGrid) {
            productsGrid.classList.add('loading');
            
            // Simuler le temps de chargement
            setTimeout(() => {
                productsGrid.classList.remove('loading');
                console.log('Produits filtrés chargés');
            }, 500);
        }
    }

    function sortProducts(sortValue) {
        console.log('Tri appliqué:', sortValue);
        // Animation de chargement ici
        setTimeout(() => {
            // Simuler le temps de chargement
            console.log('Produits triés chargés');
        }, 500);
    }

    function loadPage(pageNumber) {
        console.log('Chargement de la page:', pageNumber);
        // Animation de chargement ici
        setTimeout(() => {
            // Simuler le temps de chargement
            console.log('Page chargée');
        }, 500);
    }
}); 