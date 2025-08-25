<?php
require 'config.php';

// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Veuillez vous connecter pour ajouter aux favoris']);
    exit();
}

// Vérifier les données reçues
if (!isset($_POST['product_id']) || !isset($_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit();
}

$userId = $_SESSION['user_id'];
$productId = (int)$_POST['product_id'];
$action = $_POST['action'];

// Valider l'action
if (!in_array($action, ['add', 'remove'])) {
    echo json_encode(['success' => false, 'message' => 'Action non valide']);
    exit();
}

try {
    if ($action === 'add') {
        // Vérifier si le favori existe déjà
        $stmt = $pdo->prepare("SELECT id FROM user_favorites WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        
        if (!$stmt->fetch()) {
            // Ajouter aux favoris
            $stmt = $pdo->prepare("INSERT INTO user_favorites (user_id, product_id, created_at) VALUES (?, ?, NOW())");
            $stmt->execute([$userId, $productId]);
        }
        
        echo json_encode(['success' => true, 'action' => 'added']);
        
    } else {
        // Supprimer des favoris
        $stmt = $pdo->prepare("DELETE FROM user_favorites WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        
        echo json_encode(['success' => true, 'action' => 'removed']);
    }
    
} catch (PDOException $e) {
    error_log('Erreur lors de la mise à jour des favoris: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Une erreur est survenue']);
}
?>
