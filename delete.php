<?php
require 'session_admin.php';
require 'config.php';
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = $_GET['id'] ?? null;
    $csrf = $_GET['csrf_token'] ?? '';
    if (!$id || $csrf !== $_SESSION['csrf_token']) {
        header('Location: admin.php');
        exit;
    }
    $stmt = $pdo->prepare('SELECT image FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    if ($product) {
        @unlink('img/products/'.$product['image']);
        $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([$id]);
        $_SESSION['flash'] = 'Produit supprimé avec succès !';
    }
}
header('Location: admin.php');
exit; 