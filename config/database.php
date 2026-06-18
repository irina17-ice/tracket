<?php
// config/database.php - Version avec variables d'environnement

// Charger le fichier .env
$env = parse_ini_file(__DIR__ . '/../.env');

// Récupérer les variables
$host = $env['DB_HOST'] ?? 'localhost';
$dbname = $env['DB_NAME'] ?? 'tracket';
$username = $env['DB_USER'] ?? 'root';
$password = $env['DB_PASSWORD'] ?? '';

try {
    // Connexion avec les variables
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

session_start();

// Constantes du site
define('SITE_NAME', $env['SITE_NAME'] ?? 'trAcket');
define('SITE_URL', $env['SITE_URL'] ?? 'http://localhost:8888/');
define('MAX_ATTEMPTS', $env['MAX_ATTEMPTS'] ?? 3);
define('PASSING_SCORE', $env['PASSING_SCORE'] ?? 60);
?>