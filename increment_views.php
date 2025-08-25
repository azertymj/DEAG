<?php
require 'config.php';

// Vérifier si l'ID du produit est fourni
if (!isset($_POST['product_id']) || !is_numeric($_POST['product_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de produit invalide']);
    exit;
}

$productId = (int)$_POST['product_id'];

try {
    // Préparer et exécuter la requête d'incrémentation
    $stmt = $pdo->prepare("UPDATE products SET views = views + 1 WHERE id = :id");
    $stmt->execute([':id' => $productId]);
    
    // Vérifier si la mise à jour a réussi
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Produit non trouvé']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données']);
}
