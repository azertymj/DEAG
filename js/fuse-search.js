// Intégration Fuse.js pour la recherche dynamique catalogue
// Inclure Fuse.js via CDN dans catalogue.php
// Ce script suppose que chaque .product-card a des data-attributes pour la recherche

document.addEventListener('DOMContentLoaded', function () {
    // Charger Fuse.js dynamiquement si besoin
    if (typeof Fuse === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/fuse.js@6.6.2/dist/fuse.min.js';
        script.onload = initFuseSearch;
        document.head.appendChild(script);
    } else {
        initFuseSearch();
    }

    function initFuseSearch() {
        const searchInput = document.querySelector('.search-bar');
        const productsGrid = document.querySelector('.products-grid');
        if (!searchInput || !productsGrid) return;

        // Récupérer tous les produits depuis le DOM
        const productCards = Array.from(productsGrid.querySelectorAll('.product-card'));
        const products = productCards.map(card => {
            const name = card.querySelector('.product-name')?.textContent || '';
            const category = card.querySelector('.product-category')?.textContent || '';
            const animal_type = card.querySelector('.product-animal')?.textContent || '';
            const description = card.querySelector('.product-description')?.textContent || '';
            return { element: card, name, category, animal_type, description };
        });

        // Config Fuse.js : recherche sur le nom, la catégorie, l'animal_type, la description
        const fuse = new Fuse(products, {
            keys: [
                'name',
                'category',
                'animal_type',
                'description',
            ],
            threshold: 0.4, // tolérance plus large
            ignoreLocation: true
        });

        // Recherche dynamique à chaque frappe
        searchInput.addEventListener('input', function () {
            const query = searchInput.value.trim();
            if (!query) {
                // Afficher tous les produits
                productCards.forEach(card => card.style.display = '');
                return;
            }
            const results = fuse.search(query);
            const found = new Set(results.map(r => r.item.element));
            productCards.forEach(card => {
                card.style.display = found.has(card) ? '' : 'none';
            });
        });
    }
});
