<?php
// manager/profile_put.php
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

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['name'], $data['email'], $data['phone'], $data['status'])) {
    http_response_code(400);
    echo json_encode(['error'=>'Missing fields']);
    exit;
}
$sql = "UPDATE users SET name = ?, email = ?, phone = ?, status = ? WHERE id = ?";
$stmt = $pdo->prepare($sql);
try {
    $stmt->execute([$data['name'], $data['email'], $data['phone'], $data['status'], $userId]);
    echo json_encode(['message'=>'Profile updated']);
} catch (PDOException $e) {
    http_response_code(400);
    echo json_encode(['error'=>'Update failed']);
}
