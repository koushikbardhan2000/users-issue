<?php
// manager/assign_engineer.php
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
if (!isset($data['call_id'], $data['engineer_id'])) {
    http_response_code(400);
    echo json_encode(['error'=>'Missing parameters']);
    exit;
}
$callId = $data['call_id'];
$engId  = $data['engineer_id'];
// Verify call
$stmt = $pdo->prepare("SELECT status FROM calls WHERE id = ?");
$stmt->execute([$callId]);
$call = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$call) {
    http_response_code(404);
    echo json_encode(['error'=>'Call not found']);
    exit;
}
if ($call['status'] !== 'PENDING') {
    http_response_code(400);
    echo json_encode(['error'=>'Call is not pending']);
    exit;
}
// Verify engineer exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE id = ? AND role = 'SUPPORT_ENGINEER'");
$stmt->execute([$engId]);
if (!$stmt->fetch()) {
    http_response_code(404);
    echo json_encode(['error'=>'Engineer not found']);
    exit;
}
// Assign engineer
$stmt = $pdo->prepare("UPDATE calls SET status='ONGOING', manager_id = ?, engineer_id = ? WHERE id = ?");
try {
    $stmt->execute([$userId, $engId, $callId]);
    echo json_encode(['message'=>'Engineer assigned']);
} catch (PDOException $e) {
    http_response_code(400);
    echo json_encode(['error'=>'Assignment failed']);
}
