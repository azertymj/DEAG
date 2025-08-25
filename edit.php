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
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: admin.php');
    exit;
}
$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) {
    header('Location: admin.php');
    exit;
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
        $imgName = $product['image'];
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
        if (!$name || !$description || !$price || !$animal_type || !$category) {
            $error = 'Tous les champs obligatoires doivent être remplis.';
        } elseif (!is_numeric($price) || $price < 0) {
            $error = 'Prix invalide.';
        } elseif ($image && $image['error'] === 0) {
            $ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($ext, $allowed)) {
                $error = 'Format d\'image non autorisé.';
            } else {
                $imgName = uniqid().'.'.$ext;
                move_uploaded_file($image['tmp_name'], 'img/products/'.$imgName);
            }
        }
        if (!$error) {
            $stmt = $pdo->prepare('UPDATE products SET name=?, description=?, price=?, image=?, animal_type=?, category=?, is_in_stock=?, is_promotion=?, is_new=?, popularity=?, created_at=?, product_code=?, produit_similaire=?, capacity=?, material=?, usage_stage=?, unit=?, notes=? WHERE id=?');
            $stmt->execute([$name, $description, $price, $imgName, $animal_type, $category, $is_in_stock, $is_promotion, $is_new, $popularity, $created_at, $product_code, $produit_similaire, $capacity, $material, $usage_stage, $unit, $notes, $id]);
            $_SESSION['flash'] = 'Produit modifié avec succès !';
            header('Location: admin.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un produit - Administration</title>
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
            display: flex;
            justify-content: flex-end;
            gap: 10px;
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
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .btn-secondary {
            background: #95a5a6;
            color: white;
            margin-right: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .btn-secondary:hover {
            background: #7f8c8d;
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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
        .current-image {
            max-width: 200px;
            max-height: 200px;
            border: 1px solid #ddd;
            padding: 5px;
            margin: 10px 0;
            border-radius: 4px;
            display: block;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Modifier le produit</h1>
        
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['flash'])): ?>
            <div class="success" style="background: #e8f8e8; color: #27ae60; padding: 15px; margin-bottom: 20px; border-radius: 4px; border-left: 4px solid #2ecc71;">
                <?= htmlspecialchars($_SESSION['flash']) ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" class="admin-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            
            <!-- Section Informations de base -->
            <div class="form-section">
                <h2>Informations de base</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name" class="required">Nom du produit</label>
                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="product_code">Code produit</label>
                        <input type="text" id="product_code" name="product_code" maxlength="50" value="<?= htmlspecialchars($product['product_code']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="price" class="required">Prix (FCFA)</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" value="<?= htmlspecialchars($product['price']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="image">Image du produit</label>
                        <div style="margin-bottom: 10px;">
                            <img src="img/products/<?= htmlspecialchars($product['image']) ?>" alt="Image actuelle" class="current-image" id="currentImage">
                        </div>
                        <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(this, 'newImagePreview')">
                        <div style="margin-top: 10px;">
                            <img id="newImagePreview" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" alt="Nouvelle image" style="max-width: 200px; max-height: 200px; display: none; border: 1px solid #ddd; padding: 5px; margin-top: 10px; border-radius: 4px;">
                        </div>
                        <small>Laissez vide pour conserver l'image actuelle. Formats acceptés : JPG, PNG, GIF (max 2MB)</small>
                    </div>
                </div>
            </div>
            
            <!-- Section Détails du produit -->
            <div class="form-section">
                <h2>Détails du produit</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="description" class="required">Description</label>
                        <textarea id="description" name="description" required><?= htmlspecialchars($product['description']) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="animal_type" class="required">Type d'animal</label>
                        <select id="animal_type" name="animal_type" class="form-control" required>
                            <option value="volaille" <?= $product['animal_type']==='volaille'?'selected':'' ?>>Volaille</option>
                            <option value="porc" <?= $product['animal_type']==='porc'?'selected':'' ?>>Porc</option>
                            <option value="bovin" <?= $product['animal_type']==='bovin'?'selected':'' ?>>Bovin</option>
                            <option value="caprin" <?= $product['animal_type']==='caprin'?'selected':'' ?>>Caprin</option>
                            <option value="cheval" <?= $product['animal_type']==='cheval'?'selected':'' ?>>Cheval</option>
                            <option value="multi" <?= $product['animal_type']==='multi'?'selected':'' ?>>Multi-espèces</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="category" class="required">Catégorie</label>
                        <select id="category" name="category" class="form-control" required>
                            <option value="aliments" <?= $product['category']==='aliments'?'selected':'' ?>>Aliments</option>
                            <option value="équipements" <?= $product['category']==='équipements'?'selected':'' ?>>Équipements</option>
                            <option value="vétérinaire" <?= $product['category']==='vétérinaire'?'selected':'' ?>>Produits Vétérinaires</option>
                            <option value="accessoires" <?= $product['category']==='accessoires'?'selected':'' ?>>Accessoires</option>
                            <option value="transport" <?= $product['category']==='transport'?'selected':'' ?>>Transport</option>
                            <option value="chauffage" <?= $product['category']==='chauffage'?'selected':'' ?>>Chauffage</option>
                            <option value="éclosion" <?= $product['category']==='éclosion'?'selected':'' ?>>Éclosion</option>
                            <option value="emballage" <?= $product['category']==='emballage'?'selected':'' ?>>Emballage</option>
                            <option value="divers" <?= $product['category']==='divers'?'selected':'' ?>>Divers</option>
                            <option value="rechange" <?= $product['category']==='rechange'?'selected':'' ?>>Rechange</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="is_in_stock" class="required">En stock</label>
                        <select id="is_in_stock" name="is_in_stock" class="form-control" required>
                            <option value="oui" <?= $product['is_in_stock']==='oui'?'selected':'' ?>>Oui</option>
                            <option value="non" <?= $product['is_in_stock']==='non'?'selected':'' ?>>Non</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="is_promotion">En promotion</label>
                        <select id="is_promotion" name="is_promotion" class="form-control">
                            <option value="non" <?= $product['is_promotion']==='non'?'selected':'' ?>>Non</option>
                            <option value="oui" <?= $product['is_promotion']==='oui'?'selected':'' ?>>Oui</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="is_new">Nouveau produit</label>
                        <select id="is_new" name="is_new" class="form-control">
                            <option value="non" <?= $product['is_new']==='non'?'selected':'' ?>>Non</option>
                            <option value="oui" <?= $product['is_new']==='oui'?'selected':'' ?>>Oui</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="popularity">Popularité</label>
                        <input type="number" id="popularity" name="popularity" class="form-control" value="<?= htmlspecialchars($product['popularity']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="created_at">Date d'ajout</label>
                        <input type="date" id="created_at" name="created_at" class="form-control" value="<?= htmlspecialchars($product['created_at']) ?>">
                    </div>
                </div>
            </div>

            <!-- Section Caractéristiques -->
            <div class="form-section">
                <h2>Caractéristiques</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="produit_similaire">Produits similaires</label>
                        <input type="text" id="produit_similaire" name="produit_similaire" class="form-control" value="<?= htmlspecialchars($product['produit_similaire']) ?>" placeholder="Codes séparés par des virgules">
                    </div>
                    <div class="form-group">
                        <label for="capacity">Capacité</label>
                        <input type="text" id="capacity" name="capacity" class="form-control" value="<?= htmlspecialchars($product['capacity']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="material">Matériau</label>
                        <input type="text" id="material" name="material" class="form-control" value="<?= htmlspecialchars($product['material']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="usage_stage">Stade d'utilisation</label>
                        <input type="text" id="usage_stage" name="usage_stage" class="form-control" value="<?= htmlspecialchars($product['usage_stage']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="unit">Unité</label>
                        <input type="text" id="unit" name="unit" class="form-control" value="<?= htmlspecialchars($product['unit']) ?>">
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="notes">Notes complémentaires</label>
                        <textarea id="notes" name="notes" class="form-control" rows="4"><?= htmlspecialchars($product['notes']) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="admin.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Mettre à jour le produit
                </button>
            </div>
        </form>
    </div>

    <!-- Ajout de Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <script>
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const currentImage = document.getElementById('currentImage');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    if (currentImage) currentImage.style.display = 'none';
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.src = '';
                preview.style.display = 'none';
                if (currentImage) currentImage.style.display = 'block';
            }
        }
    </script>
</body>
</html>