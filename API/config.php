<?php
// config.php
$host     = 'localhost';
$db_name  = 'issue';
$db_user  = 'root';
$db_pass  = '';
$jwt_secret = 'b92c840f9a27e847a91ebf78aa14c6df';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    // Use exceptions for errors
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}
