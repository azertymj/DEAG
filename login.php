<?php
session_start();
require 'config.php';
// Génération du token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
// Message flash déconnexion
$logout_message = '';
if (isset($_SESSION['logout_message'])) {
    $logout_message = $_SESSION['logout_message'];
    unset($_SESSION['logout_message']);
}
// Message expiration session
$timeout_message = '';
if (isset($_GET['timeout'])) {
    $timeout_message = "Votre session a expiré, veuillez vous reconnecter.";
}
// Anti-bruteforce : limiter à 5 tentatives, blocage 10 min
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['login_blocked_until'] = 0;
}
$error = '';
if ($_SESSION['login_blocked_until'] > time()) {
    $error = "Trop de tentatives. Réessayez dans " . (($_SESSION['login_blocked_until'] - time()) + 1) . " secondes.";
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Token CSRF invalide.';
    } else {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $stmt = $pdo->prepare('SELECT * FROM admin WHERE username = ?');
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin'] = $admin['username'];
            $_SESSION['login_attempts'] = 0;
            $_SESSION['login_blocked_until'] = 0;
            header('Location: admin.php');
            exit;
        } else {
            $_SESSION['login_attempts']++;
            if ($_SESSION['login_attempts'] >= 5) {
                $_SESSION['login_blocked_until'] = time() + 600; // 10 minutes
                $error = "Trop de tentatives. Réessayez dans 10 minutes.";
            } else {
                $error = 'Identifiants invalides.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Admin</title>
    <link rel="stylesheet" href="css/components/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-form-container">
        <img src="img/logo.png" alt="Logo DEAG" class="login-logo">
        <div class="welcome-msg">Bienvenue sur l’espace d’administration DEAG</div>
        <h1>Connexion Admin</h1>
        <?php if ($logout_message): ?><div class="flash-success"><?= htmlspecialchars($logout_message) ?></div><?php endif; ?>
        <?php if ($timeout_message): ?><div class="error"><?= htmlspecialchars($timeout_message) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form class="admin-form" method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <label>Nom d'utilisateur :
                <input type="text" name="username" required autofocus>
            </label>
            <label>Mot de passe :
                <input type="password" name="password" required>
            </label>
            <button type="submit">Se connecter</button>
        </form>
        <a href="catalogue.php" class="back-link">&larr; Retour au site public</a>
    </div>
</body>
</html> 