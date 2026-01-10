<?php
require '../config.php';
require '../../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header('Content-Type: application/json');

// Validate JWT
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';
$token = str_replace('Bearer ', '', $authHeader);

try {
    $decoded = JWT::decode($token, new Key($jwt_secret, 'HS256'));
    $role = $decoded->data->role ?? null;
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid token']);
    exit;
}

if ($role !== 'MANAGER') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit;
}

// Get interval
$days = isset($_GET['days']) && is_numeric($_GET['days']) ? intval($_GET['days']) : 7;

$stmt = $pdo->prepare("SELECT COUNT(*) AS total_calls FROM calls WHERE created_at >= NOW() - INTERVAL ? DAY");
$stmt->execute([$days]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'status' => 'success',
    'days' => $days,
    'total_calls' => (int)$result['total_calls']
]);
