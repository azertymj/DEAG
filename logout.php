<?php
session_start();
// Message flash pour la déconnexion
$_SESSION['logout_message'] = 'Vous avez été déconnecté avec succès.';
session_unset();
session_destroy();
header('Location: login.php?logout=1');
exit; 