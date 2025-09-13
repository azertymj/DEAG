<?php
require 'config.php';

// Configuration de la pagination
$itemsPerPage = 12; // Nombre d'éléments par page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

// Initialisation des filtres
$filters = [
    'search' => $_GET['search'] ?? '',
    'animals' => $_GET['animals'] ?? [],
    'categories' => $_GET['categories'] ?? [],
    'availability' => $_GET['availability'] ?? [],
    'sort' => $_GET['sort'] ?? 'recent'
];
// DEBUG : log des filtres reçus
error_log('Filtres reçus : ' . print_r($filters, true));
echo '<!-- FILTRES: ' . htmlspecialchars(json_encode($filters)) . ' -->';


// Construction de la requête SQL
$sql = "SELECT * FROM products WHERE 1=1";
$params = [];

// Filtre de recherche - uniquement sur le nom du produit
if (!empty($filters['search'])) {
    $sql .= " AND name LIKE :search";
    $params[':search'] = "%{$filters['search']}%";
}

// Filtre par type d'animal
if (!empty($filters['animals'])) {
    $animalConditions = [];
    foreach ((array)$filters['animals'] as $i => $animal) {
        $placeholder = ":animal$i";
        $animalConditions[] = "animal_type LIKE $placeholder";
        $params[$placeholder] = "%$animal%";
    }
    if (!empty($animalConditions)) {
        $sql .= " AND (" . implode(' OR ', $animalConditions) . ")";
    }
}

// Filtre par catégorie
if (!empty($filters['categories'])) {
    $categoryConditions = [];
    foreach ((array)$filters['categories'] as $i => $category) {
        $placeholder = ":category$i";
        $categoryConditions[] = "category LIKE $placeholder";
        $params[$placeholder] = "%$category%";
    }
    if (!empty($categoryConditions)) {
        $sql .= " AND (" . implode(' OR ', $categoryConditions) . ")";
    }
}

// Filtre par disponibilité
if (in_array('en-stock', (array)$filters['availability'])) {
    $sql .= " AND (is_in_stock = 1 OR is_in_stock = '1' OR LOWER(is_in_stock) = 'oui')";
}
if (in_array('nouveaute', (array)$filters['availability'])) {
    $sql .= " AND is_new = 1";
}

// Tri des résultats
$orderBy = '';
switch ($filters['sort']) {
    case 'price-asc':
        $orderBy = "price ASC";
        break;
    case 'price-desc':
        $orderBy = "price DESC";
        break;
    case 'popular':
        // Utilisation de l'ID comme solution de repli si pas de colonne views
        $orderBy = "id DESC";
        break;
    case 'recent':
    default:
        $orderBy = "created_at DESC";
}

// Construction de la requête de comptage (sans le ORDER BY)
$countSql = "SELECT COUNT(*) as total FROM products WHERE 1=1" . 
    (!empty($filters['search']) ? " AND name LIKE :search" : "") .
    (!empty($filters['animals']) ? " AND animal_type IN (" . implode(',', array_fill(0, count((array)$filters['animals']), '?')) . ")" : "") .
    (!empty($filters['categories']) ? " AND category IN (" . implode(',', array_fill(0, count((array)$filters['categories']), '?')) . ")" : "") .
    (in_array('en-stock', (array)$filters['availability']) ? " AND (is_in_stock = 1 OR is_in_stock = '1' OR LOWER(is_in_stock) = 'oui')" : "") .
    (in_array('nouveaute', (array)$filters['availability']) ? " AND is_new = 1" : "");

// Ajout du tri à la requête principale
$sql .= " ORDER BY " . $orderBy;

// Exécution de la requête de comptage
$countStmt = $pdo->prepare($countSql);
$paramIndex = 1;

// Liaison des paramètres pour la requête de comptage
if (!empty($filters['search'])) {
    $countStmt->bindValue($paramIndex++, "%{$filters['search']}%");
}
if (!empty($filters['animals'])) {
    foreach ((array)$filters['animals'] as $animal) {
        $countStmt->bindValue($paramIndex++, $animal);
    }
}
if (!empty($filters['categories'])) {
    foreach ((array)$filters['categories'] as $category) {
        $countStmt->bindValue($paramIndex++, $category);
    }
}

$countStmt->execute();
$totalItems = $countStmt->fetchColumn();
$totalPages = ceil($totalItems / $itemsPerPage);

// Ajout de la pagination à la requête principale
$sql .= " LIMIT :limit OFFSET :offset";
$params[':limit'] = $itemsPerPage;
$params[':offset'] = $offset;

// DEBUG : log de la requête SQL et des paramètres
error_log('SQL : ' . $sql);
error_log('PARAMS : ' . print_r($params, true));

// Exécution de la requête principale
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// DEBUG : log du nombre de produits trouvés
error_log('Produits trouvés : ' . count($products));
echo '<!-- NB_PRODUITS: ' . count($products) . ' -->';

// Fonction pour générer l'URL avec les paramètres de filtre
function build_filter_url($newParams = []) {
    $currentParams = [
        'search' => $_GET['search'] ?? '',
        'animals' => $_GET['animals'] ?? [],
        'categories' => $_GET['categories'] ?? [],
        'availability' => $_GET['availability'] ?? [],
        'sort' => $_GET['sort'] ?? 'recent',
        'page' => $_GET['page'] ?? 1
    ];
    
    $mergedParams = array_merge($currentParams, $newParams);
    
    // Nettoyage des paramètres vides
    $mergedParams = array_filter($mergedParams, function($value) {
        return $value !== '' && $value !== [];
    });
    
    return 'catalogue.php?' . http_build_query($mergedParams);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue - DEAG</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/pages/catalogue.css">
    <link rel="stylesheet" href="css/components/chatbot.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="contact-info">
                <a href="tel:+24174000000"><i class="fas fa-phone"></i> +241 74 00 00 00</a>
                <a href="mailto:contact@elevage-pro-gabon.com"><i class="fas fa-envelope"></i> contact@elevage-pro-gabon.com</a>
            </div>
            <div class="social-links">
                <!-- <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin"></i></a> -->
            </div>
        </div>
    </div>
    <!-- Header -->
    <header class="main-header">
        <div class="container">
            <div class="logo-container">
                <a href="index.html">
                    <img src="img/logo.png" alt="logo DEAG">
                </a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="index.html">Accueil</a></li>
                    <li><a href="about.html">À propos</a></li>
                    <li><a href="catalogue.php" class="active">Catalogue</a></li>
                    <li><a href="actualites.html">Actualités</a></li>
                    <li><a href="contact.html">Contact</a></li>
                </ul>
            </nav>
            <div class="header-actions">
                <a href="https://wa.me/24174000000" class="whatsapp-btn" target="_blank">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
            </div>
            <button class="burger-menu" aria-label="Menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </header>
    <!-- Menu Mobile -->
    <div class="mobile-menu">
        <button class="mobile-menu-close" aria-label="Fermer le menu">
            <i class="fas fa-times"></i>
        </button>
        <nav>
            <ul>
                <li><a href="index.html">Accueil</a></li>
                <li><a href="about.html">À propos</a></li>
                <li><a href="catalogue.php" class="active">Catalogue</a></li>
                <li><a href="actualites.html">Actualités</a></li>
                <li><a href="contact.html">Contact</a></li>
            </ul>
        </nav>
        <div class="mobile-contact">
            <a href="tel:+24174000000" class="phone-link">
                <i class="fas fa-phone"></i> +241 74 00 00 00
            </a>
            <a href="https://wa.me/24174000000" class="whatsapp-link">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </a>
        </div>
    </div>
    <main class="catalogue-main">
        <div class="container">
            <h1>Catalogue des Produits</h1>
            <!-- Options de tri et filtres -->
            <div class="catalogue-actions">
                <button class="filters-toggle">
                    <i class="fas fa-filter"></i> Filtres
                    <span class="filters-count">0</span>
                </button>
                <div class="sort-options">
                    <form method="GET" action="catalogue.php" class="sort-form">
                        <select name="sort" class="sort-select" onchange="this.form.submit()">
                            <option value="popular" <?= $filters['sort'] === 'popular' ? 'selected' : '' ?>>Popularité</option>
                            <option value="recent" <?= $filters['sort'] === 'recent' ? 'selected' : '' ?>>Plus récents</option>
                            <option value="price-asc" <?= $filters['sort'] === 'price-asc' ? 'selected' : '' ?>>Prix croissant</option>
                            <option value="price-desc" <?= $filters['sort'] === 'price-desc' ? 'selected' : '' ?>>Prix décroissant</option>
                        </select>
                        <!-- Champs cachés pour conserver les autres paramètres de filtre -->
                        <input type="hidden" name="search" value="<?= htmlspecialchars($filters['search']) ?>">
                        <?php foreach ((array)$filters['animals'] as $animal): ?>
                            <input type="hidden" name="animals[]" value="<?= htmlspecialchars($animal) ?>">
                        <?php endforeach; ?>
                        <?php foreach ((array)$filters['categories'] as $category): ?>
                            <input type="hidden" name="categories[]" value="<?= htmlspecialchars($category) ?>">
                        <?php endforeach; ?>
                        <?php foreach ((array)$filters['availability'] as $avail): ?>
                            <input type="hidden" name="availability[]" value="<?= htmlspecialchars($avail) ?>">
                        <?php endforeach; ?>
                    </form>
                </div>
            </div>
            <div class="catalogue-content">
                <!-- Filtres -->
                <form method="GET" action="catalogue.php" class="filters-form">
<div class="filters-section">
                    <div class="filters-mobile-header">
                        <div class="filters-header-content">
                            <h2>Filtres</h2>
                            <button class="filters-close" aria-label="Fermer les filtres">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="filters-actions">
                            <button type="reset" class="btn-clear-filters">Réinitialiser</button>
<button type="submit" class="btn-apply-filters">Appliquer</button>
                        </div>
                    </div>
                    
                    <div class="filters-grid">
                        <div class="filter-group">
                            <h3>Type d'Animal</h3>
                            <div class="filter-options">
                                <label class="filter-option">
                                    <input type="checkbox" name="animals[]" value="volaille" <?= in_array('volaille', (array)$filters['animals']) ? 'checked' : '' ?>>
                                    <span>Volaille</span>
                                </label>
                                <label class="filter-option">
                                    <input type="checkbox" name="animals[]" value="porc" <?= in_array('porc', (array)$filters['animals']) ? 'checked' : '' ?>>
                                    <span>Porc</span>
                                </label>
                                <label class="filter-option">
                                    <input type="checkbox" name="animals[]" value="bovin" <?= in_array('bovin', (array)$filters['animals']) ? 'checked' : '' ?>>
                                    <span>Bovin</span>
                                </label>
                                <label class="filter-option">
                                    <input type="checkbox" name="animals[]" value="caprin" <?= in_array('caprin', (array)$filters['animals']) ? 'checked' : '' ?>>
                                    <span>Caprin</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="filter-group">
                            <h3>Catégorie</h3>
                            <div class="filter-options">
                                <label class="filter-option">
                                    <input type="checkbox" name="categories[]" value="aliments" <?= in_array('aliments', (array)$filters['categories']) ? 'checked' : '' ?>>
                                    <span>Aliments</span>
                                </label>
                                <label class="filter-option">
                                    <input type="checkbox" name="categories[]" value="équipements" <?= in_array('équipements', (array)$filters['categories']) ? 'checked' : '' ?>>
                                    <span>Équipements</span>
                                </label>
                                <label class="filter-option">
                                    <input type="checkbox" name="categories[]" value="vétérinaire" <?= in_array('vétérinaire', (array)$filters['categories']) ? 'checked' : '' ?>>
                                    <span>Produits Vétérinaires</span>
                                </label>
                                <label class="filter-option">
                                    <input type="checkbox" name="categories[]" value="accessoires" <?= in_array('accessoires', (array)$filters['categories']) ? 'checked' : '' ?>>
                                    <span>Accessoires</span>
                                </label>
                            </div>
                        </div>

                        <div class="filter-group">
                            <h3>Disponibilité</h3>
                            <div class="filter-options">
                                <label class="filter-option">
                                    <input type="checkbox" name="availability[]" value="en-stock" <?= in_array('en-stock', (array)$filters['availability']) ? 'checked' : '' ?>>
                                    <span>En Stock</span>
                                </label>
                                <label class="filter-option">
                                    <input type="checkbox" name="availability[]" value="nouveaute" <?= in_array('nouveaute', (array)$filters['availability']) ? 'checked' : '' ?>>
                                    <span>Nouveautés</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                            <!-- Barre de recherche unique -->
            <div class="search-container">
                <form method="GET" action="catalogue.php" class="search-wrapper">
                    <input type="text" name="search" class="search-bar" placeholder="Rechercher un produit..." value="<?= htmlspecialchars($filters['search']) ?>">
                    <button type="submit" class="search-button">
                        <i class="fas fa-search"></i>
                    </button>
                    <?php if (!empty($filters['search'])): ?>
                        <a href="<?= build_filter_url(['search' => '']) ?>" class="clear-search" title="Effacer la recherche">
                            <i class="fas fa-times"></i>
                        </a>
                    <?php endif; ?>
                    <!-- Champs cachés pour conserver les autres paramètres de filtre -->
                    <?php foreach ((array)$filters['animals'] as $animal): ?>
                        <input type="hidden" name="animals[]" value="<?= htmlspecialchars($animal) ?>">
                    <?php endforeach; ?>
                    <?php foreach ((array)$filters['categories'] as $category): ?>
                        <input type="hidden" name="categories[]" value="<?= htmlspecialchars($category) ?>">
                    <?php endforeach; ?>
                    <?php foreach ((array)$filters['availability'] as $avail): ?>
                        <input type="hidden" name="availability[]" value="<?= htmlspecialchars($avail) ?>">
                    <?php endforeach; ?>
                    <input type="hidden" name="sort" value="<?= htmlspecialchars($filters['sort']) ?>">
                </form>
                <div class="search-suggestions"></div>
            </div>
                <!-- Grille des produits dynamique -->
                <div class="products-grid">
                    <?php foreach ($products as $p): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php if ($p['is_new'] == 1): ?>
                                <div class="product-badge new">Nouveau</div>
                            <?php endif; ?>
                            <?php if ($p['is_promotion'] == 1): ?>
                                <div class="product-badge promotion">Promo</div>
                            <?php endif; ?>
                            <?php 
                            $imagePath = 'img/products/' . htmlspecialchars($p['image']);
                            if (!file_exists($imagePath) || empty($p['image'])) {
                                $imagePath = 'img/placeholder.jpg'; // Image par défaut si l'image n'existe pas
                            }
                            ?>
                            <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                        </div>
                        <div class="product-info">
                            <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
                            <div class="product-category"><?= htmlspecialchars($p['category']) ?></div>
                            <?php if (!empty($p['animal_type'])): ?>
                                <div class="product-animal"><?= htmlspecialchars($p['animal_type']) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($p['capacity'])): ?>
                                <div class="product-capacity"><?= htmlspecialchars($p['capacity']) ?></div>
                            <?php endif; ?>
                            <?php 
                            // Vérifier si le produit est en stock (gère à la fois les booléens et les chaînes 'oui'/'non')
                            $isInStock = ($p['is_in_stock'] === true || $p['is_in_stock'] === 1 || strtolower($p['is_in_stock']) === 'oui' || $p['is_in_stock'] === '1');
                            ?>
                            <div class="product-availability <?= $isInStock ? 'in-stock' : 'out-of-stock' ?>">
                                <i class="fas <?= $isInStock ? 'fa-check-circle' : 'fa-times-circle' ?>"></i>
                                <?= $isInStock ? 'En stock' : 'Rupture de stock' ?>
                            </div>
                            <div class="product-actions">
                                <a href="product-view.php?id=<?= $p['id'] ?>" class="btn btn-primary">
                                    <i class="fas fa-eye"></i> Voir détails
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <!-- Bouton Précédent -->
                        <a href="<?= $page > 1 ? build_filter_url(['page' => $page - 1]) : '#' ?>" class="pagination-btn <?= $page <= 1 ? 'disabled' : '' ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                        
                        <!-- Numéros de page -->
                        <?php
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $startPage + 4);
                        if ($endPage - $startPage < 4) {
                            $startPage = max(1, $endPage - 4);
                        }
                        
                        // Premier bouton
                        if ($startPage > 1) {
                            echo '<a href="' . build_filter_url(['page' => 1]) . '" class="pagination-btn">1</a>';
                            if ($startPage > 2) {
                                echo '<span class="pagination-ellipsis">...</span>';
                            }
                        }
                        
                        // Boutons du milieu
                        for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <a href="<?= build_filter_url(['page' => $i]) ?>" class="pagination-btn <?= $i == $page ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor;
                        
                        // Dernier bouton
                        if ($endPage < $totalPages) {
                            if ($endPage < $totalPages - 1) {
                                echo '<span class="pagination-ellipsis">...</span>';
                            }
                            echo '<a href="' . build_filter_url(['page' => $totalPages]) . '" class="pagination-btn">' . $totalPages . '</a>';
                        }
                        ?>
                        
                        <!-- Bouton Suivant -->
                        <a href="<?= $page < $totalPages ? build_filter_url(['page' => $page + 1]) : '#"' ?> class="pagination-btn <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-column">
                    <h3>À propos</h3>
                    <p>DEAG, leader dans l'élevage professionnel au Gabon, s'engage à fournir des produits et services de qualité supérieure pour le développement de l'agriculture locale.</p>
                </div>
                <div class="footer-column">
                    <h3>Liens rapides</h3>
                    <ul>
                        <li><a href="about.html">À propos</a></li>
                        <li><a href="catalogue.php">Catalogue</a></li>
                        <li><a href="actualites.html">Actualités</a></li>
                        <li><a href="contact.html">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Contact</h3>
                    <ul class="contact-info">
                        <li><i class="fas fa-map-marker-alt"></i> Libreville, Gabon</li>
                        <li><i class="fas fa-phone"></i> +241 74 00 00 00</li>
                        <li><i class="fas fa-envelope"></i> contact@elevage-pro-gabon.com</li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Newsletter</h3>
                    <form class="newsletter-form">
                        <input type="email" placeholder="Votre email" required>
                        <button type="submit">S'abonner</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <p>&copy; 2025 DEAG. Tous droits réservés.</p>
                <p><i class="fab fa-github"></i><a style="color: white;" href="https://azertymj.github.io/" target="_blank"> Azertymj</a></p>
            </div>
        </div>
    </footer>
    <script src="js/script.js"></script>
    <script src="js/catalogue.js"></script>
    <script src="js/components/chatbot.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/fuse.js@6.6.2/dist/fuse.min.js"></script>
<script src="js/fuse-search.js"></script>
</body>
</html> 