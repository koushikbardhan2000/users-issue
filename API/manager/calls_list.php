<?php
// manager/calls_list.php
header('Content-Type: application/json');
require '../config.php';
require '../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Extract and decode the JWT
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';
if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
    http_response_code(401);
    echo json_encode(['error' => 'Authorization token missing or malformed']);
    exit;
}

$token = str_replace('Bearer ', '', $authHeader);
try {
    $decoded = JWT::decode($token, new Key($jwt_secret, 'HS256'));
    $userId = $decoded->data->user_id ?? null;
    $role = $decoded->data->role ?? null;
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid token']);
    exit;
}
// (JWT decode and role check)
$status = $_GET['status'] ?? '';
if (!in_array($status, ['PENDING','ONGOING','CLOSED'])) {
    http_response_code(400);
    echo json_encode(['error'=>'Invalid status']);
    exit;
}
$stmt = $pdo->prepare("SELECT * FROM calls WHERE status = ?");
$stmt->execute([$status]);
$calls = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($calls);
