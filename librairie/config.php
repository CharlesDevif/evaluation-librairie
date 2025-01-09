<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Configuration de la base de données
$host = getenv('DB_HOST') ?: 'db';
$dbname = getenv('DB_NAME') ?: 'library';
$username = getenv('DB_USER') ?: 'library_user';
$password = getenv('DB_PASS') ?: 'library_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    error_log("Erreur de connexion à la base de données : " . $e->getMessage());
    die("Erreur de connexion à la base de données.");
}
?>
