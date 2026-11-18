<?php
// config/db.php - Pour TON XAMPP
$host = "127.0.0.1";
$port = "3307"; // <-- TON port MySQL, vu sur ta photo
$dbname = "restaurant_by_mali";
$username = "root";
$password = ""; // vide sur XAMPP

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur connexion BDD : " . $e->getMessage());
}
?>