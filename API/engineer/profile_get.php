<?php
// manager/profile_get.php
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
if ($role !== 'SUPPORT_ENGINEER') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied, engineer only']);
    exit;
}
// (JWT decode omitted for brevity; assume $userId and role checked)
$stmt = $pdo->prepare("SELECT id, name, email, phone, role, status FROM users WHERE id = ?");
$stmt->execute([$userId]);
$mgr = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$mgr) {
    http_response_code(404);
    echo json_encode(['error' => 'Manager not found']);
    exit;
}
echo json_encode($mgr);
