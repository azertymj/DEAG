<?php
require 'config.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = null;
if ($id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $product = $stmt->fetch();
}
if (!$product) {
    http_response_code(404);
    echo '<h1>Produit introuvable</h1>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - DEAG</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/pages/product-view.css">
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
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin"></i></a>
            </div>
        </div>
    </div>
    <!-- Header -->
    <header class="main-header">
        <div class="container">
            <div class="logo-container">
                <a href="index.html">
                    <img src="img/logo.png" alt="DEAG">
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
    <!-- Product View Section -->
    <section class="product-view">
        <div class="product-container">
            <!-- Gallery Section -->
            <div class="product-gallery">
                <img src="img/products/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="main-image">
            </div>
            <!-- Product Info Section -->
            <div class="product-info">
                <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>

                <div class="product-description">
                    <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                </div>
                <div class="product-meta">
                    <?php if (!empty($product['product_code'])): ?>
                    <div class="meta-item">
                        <i class="fas fa-barcode"></i>
                        <span>Réf: <?= htmlspecialchars($product['product_code']) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="meta-item">
                        <i class="fas <?= $product['is_in_stock'] ? 'fa-check-circle' : 'fa-times-circle' ?>"></i>
                        <span><?= $product['is_in_stock'] ? 'En stock' : 'Rupture de stock' ?></span>
                    </div>
                    
                    <?php if (!empty($product['animal_type'])): ?>
                    <div class="meta-item">
                        <i class="fas fa-paw"></i>
                        <span>Type: <?= htmlspecialchars($product['animal_type']) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($product['category'])): ?>
                    <div class="meta-item">
                        <i class="fas fa-tag"></i>
                        <span>Catégorie: <?= htmlspecialchars($product['category']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php 
                // Afficher la description complète
                if (!empty($product['description'])): 
                ?>
                <div class="product-full-description">
                    <h3>Description détaillée</h3>
                    <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                </div>
                <?php 
                endif; 
                
                // Afficher les notes si elles existent
                if (!empty($product['notes'])): 
                ?>
                <div class="product-notes">
                    <h3>Notes importantes</h3>
                    <p><?= nl2br(htmlspecialchars($product['notes'])) ?></p>
                </div>
                <?php endif; ?>
                
                <div class="product-actions">
                    <a href="catalogue.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour au catalogue
                    </a>
                    <button type="button" class="btn btn-chat" onclick="askAboutProduct('<?= addslashes(htmlspecialchars($product['name'])) ?>')">
                        <i class="fas fa-comments"></i> Questions sur ce produit
                    </button>
                </div>
            </div>
        </div>
    </section>
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
                    <form class="newsletter-form" action="#" method="POST">
                        <input type="email" placeholder="Votre email" required>
                        <button type="submit">S'abonner</button>
                    </form>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <p>&copy; 2025 DEAG. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
    <script>
        function askAboutProduct(productName) {
            // Activer la fenêtre de chat
            const chatWindow = document.querySelector('.chat-window');
            const chatBubble = document.querySelector('.chat-bubble');
            
            if (chatWindow && chatBubble) {
                // Afficher la fenêtre de chat et cacher la bulle
                chatWindow.classList.add('active');
                chatBubble.style.display = 'none';
                
                // Mettre le focus sur le champ de saisie après un court délai
                setTimeout(() => {
                    const chatInput = chatWindow.querySelector('input[type="text"]');
                    if (chatInput) {
                        chatInput.value = `Questions sur ${productName} ?`;
                        chatInput.focus();
                    }
                }, 100);
            } else {
                console.error('Éléments du chat introuvables');
            }
        }
    </script>
    <script src="js/script.js" defer></script>
    <script src="js/components/chatbot.js" defer></script>
    <script src="js/pages/product-view.js" defer></script>
</body>
</html> 