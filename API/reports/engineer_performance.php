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

$sql = "
    SELECT 
        u.id AS engineer_id,
        u.name AS engineer_name,
        COUNT(c.id) AS total_closed_calls
    FROM users u
    LEFT JOIN calls c ON c.engineer_id = u.id AND c.status = 'CLOSED'
    WHERE u.role = 'SUPPORT_ENGINEER'
    GROUP BY u.id, u.name
";

$stmt = $pdo->query($sql);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'status' => 'success',
    'performance' => $results
]);
