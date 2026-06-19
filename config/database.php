<?php
// config/database.php - Version Render

// Récupérer les variables d'environnement Render
$driver = getenv('DB_DRIVER') ?: 'mysql';
$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'tracket';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$port = getenv('DB_PORT') ?: '';

// Si le port n'est pas défini, valeur par défaut
if (empty($port)) {
    $port = ($driver === 'pgsql') ? '5432' : '3306';
}

try {
    if ($driver === 'pgsql') {
        // PostgreSQL (Render)
        $dsn = "pgsql:host=$host;dbname=$dbname;port=$port";
    } else {
        // MySQL (Local)
        $dsn = "mysql:host=$host;dbname=$dbname;port=$port;charset=utf8mb4";
    }
    
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

session_start();

// Constantes du site
define('SITE_NAME', getenv('SITE_NAME') ?: 'trAcket');
define('SITE_URL', getenv('SITE_URL') ?: 'http://localhost:8888/');
define('MAX_ATTEMPTS', getenv('MAX_ATTEMPTS') ?: 3);
define('PASSING_SCORE', getenv('PASSING_SCORE') ?: 60);
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
?>