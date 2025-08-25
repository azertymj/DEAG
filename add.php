<?php
require 'session_admin.php';
require 'config.php';
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Token CSRF invalide.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = $_POST['price'] ?? '';
        $image = $_FILES['image'] ?? null;
        $animal_type = $_POST['animal_type'] ?? '';
        $category = $_POST['category'] ?? '';
        $is_in_stock = $_POST['is_in_stock'] ?? 'oui';
        $is_promotion = $_POST['is_promotion'] ?? 'non';
        $is_new = $_POST['is_new'] ?? 'non';
        $popularity = $_POST['popularity'] ?? 0;
        $created_at = $_POST['created_at'] ?? date('Y-m-d');
        $product_code = trim($_POST['product_code'] ?? '');
        $produit_similaire = trim($_POST['produit_similaire'] ?? '');
        $capacity = trim($_POST['capacity'] ?? '');
        $material = trim($_POST['material'] ?? '');
        $usage_stage = trim($_POST['usage_stage'] ?? '');
        $unit = trim($_POST['unit'] ?? '');
        $notes = trim($_POST['notes'] ?? '');
        if (!$name || !$description || !$price || !$image || $image['error'] !== 0 || !$animal_type || !$category) {
            $error = 'Tous les champs obligatoires doivent être remplis.';
        } elseif (!is_numeric($price) || $price < 0) {
            $error = 'Prix invalide.';
        } else {
            $ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($ext, $allowed)) {
                $error = 'Format d\'image non autorisé.';
            } else {
                $imgName = preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($image['name']));
                $targetPath = 'img/products/'.$imgName;
                
                // Fonction d'optimisation d'image
                function optimizeImage($source, $destination, $max_width = 1200, $quality = 80) {
                    $info = getimagesize($source);
                    $mime = $info['mime'];
                    
                    // Créer une image à partir du fichier source
                    switch($mime) {
                        case 'image/jpeg':
                            $image = imagecreatefromjpeg($source);
                            break;
                        case 'image/png':
                            $image = imagecreatefrompng($source);
                            break;
                        case 'image/gif':
                            $image = imagecreatefromgif($source);
                            break;
                        default:
                            return false;
                    }
                    
                    if (!$image) return false;
                    
                    // Obtenir les dimensions originales
                    $width = imagesx($image);
                    $height = imagesy($image);
                    
                    // Calculer les nouvelles dimensions
                    if ($width > $max_width) {
                        $new_width = $max_width;
                        $new_height = floor($height * ($max_width / $width));
                    } else {
                        $new_width = $width;
                        $new_height = $height;
                    }
                    
                    // Créer une nouvelle image redimensionnée
                    $new_image = imagecreatetruecolor($new_width, $new_height);
                    
                    // Conserver la transparence pour les PNG et GIF
                    if ($mime == 'image/png' || $mime == 'image/gif') {
                        imagecolortransparent($new_image, imagecolorallocatealpha($new_image, 0, 0, 0, 127));
                        imagealphablending($new_image, false);
                        imagesavealpha($new_image, true);
                    }
                    
                    // Redimensionner l'image
                    imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                    
                    // Enregistrer l'image optimisée
                    switch($mime) {
                        case 'image/jpeg':
                            imagejpeg($new_image, $destination, $quality);
                            break;
                        case 'image/png':
                            $quality = 9 - (int)((0.9 * $quality) / 10.0);
                            imagepng($new_image, $destination, $quality);
                            break;
                        case 'image/gif':
                            imagegif($new_image, $destination);
                            break;
                    }
                    
                    // Libérer la mémoire
                    imagedestroy($image);
                    imagedestroy($new_image);
                    
                    return true;
                }
                
                // Optimiser l'image avant de l'enregistrer
                if (!optimizeImage($image['tmp_name'], $targetPath)) {
                    // Si l'optimisation échoue, enregistrer l'image normalement
                    move_uploaded_file($image['tmp_name'], $targetPath);
                }
                $stmt = $pdo->prepare('INSERT INTO products (name, description, price, image, animal_type, category, is_in_stock, is_promotion, is_new, popularity, created_at, product_code, produit_similaire, capacity, material, usage_stage, unit, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([$name, $description, $price, $imgName, $animal_type, $category, $is_in_stock, $is_promotion, $is_new, $popularity, $created_at, $product_code, $produit_similaire, $capacity, $material, $usage_stage, $unit, $notes]);
                $_SESSION['flash'] = 'Produit ajouté avec succès !';
                header('Location: admin.php');
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un produit - Administration</title>
    <link rel="stylesheet" href="css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .form-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-section {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 25px;
            overflow: hidden;
        }
        .form-section h2 {
            background: #2c3e50;
            color: white;
            padding: 15px 20px;
            margin: 0;
            font-size: 1.1em;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #333;
        }
        .form-group label.required:after {
            content: ' *';
            color: #e74c3c;
        }
        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group input[type="date"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: 'Raleway', sans-serif;
        }
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        .form-actions {
            text-align: right;
            padding: 20px;
            background: #f8f9fa;
            border-top: 1px solid #eee;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn-primary {
            background: #3498db;
            color: white;
        }
        .btn-primary:hover {
            background: #2980b9;
        }
        .btn-secondary {
            background: #95a5a6;
            color: white;
            margin-right: 10px;
        }
        .btn-secondary:hover {
            background: #7f8c8d;
        }
        .error {
            background: #fde8e8;
            color: #c0392b;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            border-left: 4px solid #e74c3c;
        }
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Ajouter un nouveau produit</h1>
        
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" class="admin-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            
            <!-- Section Informations de base -->
            <div class="form-section">
                <h2>Informations de base</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name" class="required">Nom du produit</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="product_code">Code produit</label>
                        <input type="text" id="product_code" name="product_code" maxlength="50">
                    </div>
                    <div class="form-group">
                        <label for="price" class="required">Prix (FCFA)</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="image" class="required">Image du produit</label>
                        <div style="margin-bottom: 10px;">
                            <img id="imagePreview" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" alt="Aperçu de l'image" style="max-width: 200px; max-height: 200px; display: none; border: 1px solid #ddd; padding: 5px; margin-top: 10px; border-radius: 4px;">
                        </div>
                        <input type="file" id="image" name="image" accept="image/*" required onchange="previewImage(this)">
                        <small>Formats acceptés : JPG, PNG, GIF (max 2MB)</small>
                    </div>
                </div>
            </div>

            <!-- Section Catégorisation -->
            <div class="form-section">
                <h2>Catégorisation</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="animal_type" class="required">Type d'animal</label>
                        <select id="animal_type" name="animal_type" required>
                            <option value="">-- Sélectionner --</option>
                            <option value="volaille">Volaille</option>
                            <option value="porc">Porc</option>
                            <option value="bovin">Bovin</option>
                            <option value="caprin">Caprin</option>
                            <option value="cheval">Cheval</option>
                            <option value="multi">Multi-espèces</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="category" class="required">Catégorie</label>
                        <select id="category" name="category" required>
                            <option value="">-- Sélectionner --</option>
                            <option value="aliments">Aliments</option>
                            <option value="équipements">Équipements</option>
                            <option value="vétérinaire">Produits Vétérinaires</option>
                            <option value="accessoires">Accessoires</option>
                            <option value="transport">Transport</option>
                            <option value="chauffage">Chauffage</option>
                            <option value="éclosion">Éclosion</option>
                            <option value="emballage">Emballage</option>
                            <option value="divers">Divers</option>
                            <option value="rechange">Rechange</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section Détails du produit -->
            <div class="form-section">
                <h2>Détails du produit</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="capacity">Capacité</label>
                        <input type="text" id="capacity" name="capacity" maxlength="50">
                    </div>
                    <div class="form-group">
                        <label for="material">Matériau</label>
                        <input type="text" id="material" name="material" maxlength="100">
                    </div>
                    <div class="form-group">
                        <label for="usage_stage">Stade d'utilisation</label>
                        <input type="text" id="usage_stage" name="usage_stage" maxlength="100">
                    </div>
                    <div class="form-group">
                        <label for="unit">Unité</label>
                        <input type="text" id="unit" name="unit" maxlength="50">
                    </div>
                </div>
            </div>

            <!-- Section Options -->
            <div class="form-section">
                <h2>Options</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="is_in_stock" class="required">En stock</label>
                        <select id="is_in_stock" name="is_in_stock" required>
                            <option value="oui">Oui</option>
                            <option value="non">Non</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="is_promotion" class="required">En promotion</label>
                        <select id="is_promotion" name="is_promotion" required>
                            <option value="non">Non</option>
                            <option value="oui">Oui</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="is_new" class="required">Nouveau produit</label>
                        <select id="is_new" name="is_new" required>
                            <option value="non">Non</option>
                            <option value="oui">Oui</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="popularity">Popularité (0-100)</label>
                        <input type="number" id="popularity" name="popularity" min="0" max="100" value="0">
                    </div>
                </div>
            </div>

            <!-- Section Description -->
            <div class="form-section">
                <h2>Description et détails</h2>
                <div class="form-grid">
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="description" class="required">Description détaillée</label>
                        <textarea id="description" name="description" required></textarea>
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="notes">Notes complémentaires</label>
                        <textarea id="notes" name="notes"></textarea>
                    </div>
                </div>
            </div>

            <!-- Section Produits similaires -->
            <div class="form-section">
                <h2>Produits similaires</h2>
                <div class="form-grid">
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="produit_similaire">Codes des produits similaires</label>
                        <input type="text" id="produit_similaire" name="produit_similaire" placeholder="Séparez les codes par des virgules">
                        <small>Exemple: PROD123, PROD456, PROD789</small>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-actions" style="display: flex; justify-content: space-between;">
                    <div>
                        <a href="admin.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour à la liste
                        </a>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer le produit
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Ajout de Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <script>
        // Fonction de prévisualisation de l'image
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const file = input.files[0];
            const reader = new FileReader();

            reader.onloadend = function() {
                preview.src = reader.result;
                preview.style.display = 'block';
            }

            if (file) {
                // Vérification de la taille du fichier (max 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('La taille de l\'image ne doit pas dépasser 2MB');
                    input.value = '';
                    preview.style.display = 'none';
                    return;
                }
                reader.readAsDataURL(file);
            } else {
                preview.src = 'data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==';
                preview.style.display = 'none';
            }
        }

        // Validation du formulaire côté client
        document.querySelector('form').addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#e74c3c';
                    field.addEventListener('input', function() {
                        if (this.value.trim()) {
                            this.style.borderColor = '';
                        }
                    });
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs obligatoires.');
            }
        });
    </script>
</body>
</html> 