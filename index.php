<?php
echo "<h1>trAcket LMS - Test</h1>";
echo "<p>PHP fonctionne sur Render !</p>";

// Afficher les extensions chargées
echo "<h2>Extensions PHP :</h2>";
echo "<pre>";
print_r(get_loaded_extensions());
echo "</pre>";

// Afficher les variables d'environnement
echo "<h2>Variables d'environnement :</h2>";
echo "<pre>";
print_r(getenv());
echo "</pre>";

// Tester la connexion PostgreSQL
try {
    $driver = getenv('DB_DRIVER') ?: 'mysql';
    $host = getenv('DB_HOST') ?: 'localhost';
    $dbname = getenv('DB_NAME') ?: 'tracket';
    $user = getenv('DB_USER') ?: 'root';
    $pass = getenv('DB_PASSWORD') ?: '';
    $port = getenv('DB_PORT') ?: '5432';
    
    echo "<h2>Connexion PostgreSQL :</h2>";
    
    if ($driver === 'pgsql') {
        $pdo = new PDO("pgsql:host=$host;dbname=$dbname;port=$port", $user, $pass);
        echo "<p style='color:green'>✅ Connexion PostgreSQL réussie !</p>";
    } else {
        echo "<p>Driver configuré : $driver</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Erreur : " . $e->getMessage() . "</p>";
}
?>