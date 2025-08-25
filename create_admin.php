<?php
require 'config.php';
$username = 'azeradmin';
$password = password_hash('Azertymj', PASSWORD_DEFAULT);
$stmt = $pdo->prepare('INSERT INTO admin (username, password) VALUES (?, ?)');
$stmt->execute([$username, $password]);
echo "Admin créé";
?>