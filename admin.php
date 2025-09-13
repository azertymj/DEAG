<?php
require 'session_admin.php';
require 'config.php';
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
// Message flash
$flash = '';
if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
}
$products = $pdo->query('SELECT * FROM products ORDER BY id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Gestion des produits</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="css/components/admin.css">

</head>
<body>
    <div class="container py-6 px-4 mx-auto my-4" style="max-width: 1800px; margin: 20px !important;">
        <div class="card shadow-lg mb-6 border-0" style="border-radius: 12px; overflow: hidden;">
            <div class="card-body">
        <div class="admin-header">
            <h1><i class="fas fa-boxes"></i> Gestion des produits</h1>
            <div class="admin-actions">
                <a href="add.php">
                    <i class="fas fa-plus"></i> Ajouter un produit
                </a>
                <a href="logout.php" class="logout">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>

        <?php if ($flash): ?>
            <div class="flash-success" id="flash-success">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($flash) ?>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
    <table id="productsTable" class="table table-striped" style="width:100%">
        <thead>
            <tr>
                <th>Image</th>
                <th>Nom</th>
                <th>Prix</th>
                <th>Type</th>
                <th>Catégorie</th>
                <th>Stock</th>
                <th>Promo</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
            <tr>
                <td>
                    <?php if (!empty($p['image']) && file_exists('img/products/' . $p['image'])): ?>
                        <img src="img/products/<?= htmlspecialchars($p['image']) ?>" alt="" class="product-image">
                    <?php else: ?>
                        <div class="no-image" style="width:60px;height:60px;background:#f5f5f5;display:flex;align-items:center;justify-content:center;border-radius:4px;">
                            <i class="fas fa-image" style="color:#ccc;font-size:20px;"></i>
                        </div>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="fw-bold"><?= htmlspecialchars($p['name']) ?></div>
                    <small class="text-muted"><?= htmlspecialchars($p['product_code']) ?></small>
                </td>
                <td><?= number_format($p['price'], 0, ',', ' ') ?> FCFA</td>
                <td><span class="badge bg-secondary"><?= ucfirst(htmlspecialchars($p['animal_type'])) ?></span></td>
                <td><span class="badge bg-info text-dark"><?= ucfirst(htmlspecialchars($p['category'])) ?></span></td>
                <td>
                    <span class="badge <?= $p['is_in_stock'] === 'oui' ? 'bg-success' : 'bg-danger' ?>">
                        <?= ucfirst(htmlspecialchars($p['is_in_stock'])) ?>
                    </span>
                </td>
                <td>
                    <?php if ($p['is_promotion'] === 'oui'): ?>
                        <span class="badge bg-warning text-dark">
                            <i class="fas fa-tag"></i> En promo
                        </span>
                    <?php endif; ?>
                    <?php if ($p['is_new'] === 'oui'): ?>
                        <span class="badge bg-primary mt-1 d-block">
                            <i class="fas fa-star"></i> Nouveau
                        </span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="d-flex gap-2">
                        <a href="edit.php?id=<?= $p['id'] ?>" class="btn-action btn-edit" title="Modifier">
                            <i class="fas fa-edit"></i> <span class="action-text">Modif</span>
                        </a>
                        <a href="delete.php?id=<?= $p['id'] ?>&csrf_token=<?= $_SESSION['csrf_token'] ?>" 
                           class="btn-action btn-delete" 
                           title="Supprimer"
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">
                            <i class="fas fa-trash"></i> <span class="action-text">Suppr</span>
                        </a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

            </div> <!-- Fin du card-body -->
        </div> <!-- Fin du card -->
    </div> <!-- Fin du container -->

    <!-- Scripts JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/components/admin.js"></script>
    
</body>
</html> 