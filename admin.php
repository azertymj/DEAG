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
    <style>
        .flash-success {
            background: #eafaf7;
            color: #159c85;
            border: 1px solid #b7e4da;
            padding: 12px 18px;
            border-radius: 5px;
            margin: 0 auto 24px auto;
            font-weight: 600;
            text-align: center;
            max-width: 100%;
            box-shadow: 0 2px 8px rgba(44,62,80,0.06);
        }
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }
        .admin-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        .admin-actions a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .admin-actions a:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }
        .admin-actions a.logout {
            background: #e74c3c;
        }
        .admin-actions a.logout:hover {
            background: #c0392b;
        }
        .dataTables_wrapper {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .dataTables_filter input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-left: 10px;
        }
        .dataTables_length select {
            padding: 6px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn-action {
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            font-weight: 500;
            justify-content: center;
            width: 36px;
            height: 36px;
        }
        .btn-edit {
            background: #3498db;
            color: white;
            box-shadow: 0 2px 4px rgba(52, 152, 219, 0.3);
        }
        .btn-edit:hover {
            background: #2980b9;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(41, 128, 185, 0.3);
        }
        .btn-delete {
            background: #e74c3c;
            color: white;
            box-shadow: 0 2px 4px rgba(231, 76, 60, 0.3);
        }
        .btn-delete:hover {
            background: #c0392b;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(192, 57, 43, 0.3);
        }
        .action-text {
            font-size: 0.85rem;
        }
        @media (max-width: 768px) {
            .action-text {
                display: none;
            }
            .btn-action {
                padding: 8px 10px;
                font-size: 1rem;
            }
        }
        .product-image {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #eee;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: 600;
        }
        .badge-yes {
            background: #2ecc71;
            color: white;
        }
        .badge-no {
            background: #e74c3c;
            color: white;
        }
        .table-responsive {
            overflow-x: auto;
            margin-bottom: 2rem;
        }
        
        /* Style pour la pagination */
        .dataTables_wrapper {
            padding: 0 !important;
        }
        
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 1.5rem;
            padding: 1rem 0;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            padding: 0.75rem 0;
        }
        
        /* Style des boutons de pagination */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            min-width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            font-weight: 500;
            color: #4a5568;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            margin: 0 2px;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #f7fafc;
            border-color: #cbd5e0;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #4299e1;
            color: white !important;
            border-color: #3182ce;
            font-weight: 600;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.previous,
        .dataTables_wrapper .dataTables_paginate .paginate_button.next {
            padding: 0 12px;
            min-width: auto;
            background: #f7fafc;
            font-weight: 600;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.5rem 0.75rem;
            margin: 0 0.25rem;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            color: #0d6efd;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.current, 
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #0d6efd;
            color: white !important;
            border-color: #0d6efd;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #e9ecef;
            color: #0a58ca;
            text-decoration: none;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled, 
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover, 
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:active {
            color: #6c757d !important;
            background: transparent;
            cursor: default;
        }
        
        /* Style pour les infos de pagination */
        .dataTables_info {
            margin-top: 1rem;
            color: #6c757d;
            text-align: center;
            padding: 0.5rem;
        }
        @media (max-width: 768px) {
            .admin-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .admin-actions {
                width: 100%;
                margin-top: 15px;
            }
            .admin-actions a {
                flex: 1;
                justify-content: center;
            }
        }
    </style>
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
    
    <script>
        // Initialisation de DataTable
        $(document).ready(function() {
            $('#productsTable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json',
                    paginate: {
                        previous: '&laquo;',
                        next: '&raquo;'
                    },
                    info: 'Affichage de _START_ à _END_ sur _TOTAL_ entrées',
                    lengthMenu: 'Afficher _MENU_ entrées par page',
                    search: 'Rechercher:',
                    searchPlaceholder: 'Rechercher...',
                    infoEmpty: 'Aucune entrée à afficher',
                    infoFiltered: '(filtré de _MAX_ entrées au total)'
                },
                order: [[1, 'asc']], // Tri par nom par défaut
                pageLength: 15,
                dom: "<'row mb-4'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                     "<'row'<'col-sm-12'tr>>" +
                     "<'row mt-4'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
                     .replace(/l>/g, 'l class="mb-3">')
                     .replace(/f>/g, 'f class="mb-3">'),
                columnDefs: [
                    { orderable: false, targets: [0, 7] }, // Désactive le tri sur les colonnes d'image et d'actions
                    { searchable: false, targets: [0, 5, 6, 7] } // Désable la recherche sur ces colonnes
                ],
                drawCallback: function() {
                    $('.paginate_button').addClass('btn btn-sm btn-outline-secondary mx-1');
                    $('.paginate_button.current').removeClass('btn-outline-secondary').addClass('btn-primary');
                }
            });

            // Disparition automatique du message flash après 5 secondes
            var flash = document.getElementById('flash-success');
            if (flash) {
                setTimeout(function() {
                    $(flash).fadeOut('slow');
                }, 5000);
            }

            // Confirmation avant suppression
            $('.delete').on('click', function(e) {
                if (!confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) {
                    e.preventDefault();
                }
            });
        });


    </script>
</body>
</html> 