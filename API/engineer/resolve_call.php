<?php
// engineer/resolve_call.php
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
    echo json_encode(['error' => 'Access denied: not a support engineer']);
    exit;
}

// Parse and validate input
$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['call_id'], $data['resolution_status'], $data['issue_type'], $data['remarks'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

$callId = $data['call_id'];

// Check if the call exists and is assigned to this engineer
$stmt = $pdo->prepare("SELECT status, engineer_id FROM calls WHERE id = ?");
$stmt->execute([$callId]);
$call = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$call) {
    http_response_code(404);
    echo json_encode(['error' => 'Call not found']);
    exit;
}

if ($call['status'] !== 'ONGOING' || $call['engineer_id'] != $userId) {
    http_response_code(403);
    echo json_encode(['error' => 'Not authorized or call not ongoing']);
    exit;
}

// Update call resolution status
$stmt = $pdo->prepare("UPDATE calls SET resolution_status = ?, updated_at = NOW() WHERE id = ?");
try {
    $stmt->execute([$data['resolution_status'], $callId]);
    echo json_encode(['message' => 'Call resolution status updated']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database update failed']);
}
