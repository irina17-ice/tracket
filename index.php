<?php
// index.php - Redirection vers la page de connexion
require_once 'config/database.php';

if(isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
} else {
    header('Location: login.php');
    exit();
}
?>
