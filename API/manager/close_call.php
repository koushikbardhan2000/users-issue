<?php
// manager/close_call.php
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
if ($role !== 'MANAGER') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied, manager role required']);
    exit;
}
// Parse input
$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['call_id'], $data['final_remark'])) {
    http_response_code(400);
    echo json_encode(['error'=>'Missing parameters']);
    exit;
}
$callId = $data['call_id'];
// Verify call
$stmt = $pdo->prepare("SELECT status FROM calls WHERE id = ?");
$stmt->execute([$callId]);
$call = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$call) {
    http_response_code(404);
    echo json_encode(['error'=>'Call not found']);
    exit;
}
if ($call['status'] !== 'ONGOING') {
    http_response_code(400);
    echo json_encode(['error'=>'Call is not ongoing']);
    exit;
}
// Close the call
$stmt = $pdo->prepare("UPDATE calls SET status='CLOSED', final_remark = ?, closed_at = NOW() WHERE id = ?");
try {
    $stmt->execute([$data['final_remark'], $callId]);
    echo json_encode(['message'=>'Call closed']);
} catch (PDOException $e) {
    http_response_code(400);
    echo json_encode(['error'=>'Failed to close call']);
}
