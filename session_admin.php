<?php
session_start();
// Expiration de session après 10 minutes d'inactivité
$timeout = 600;
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout)) {
    session_unset();
    session_destroy();
    header('Location: login.php?timeout=1');
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time();
// Vérification de la connexion admin
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
} 