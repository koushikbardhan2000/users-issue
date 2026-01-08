<?php
// manager/profile_get.php
header('Content-Type: application/json');
require '../config.php';
require '../vendor/autoload.php';
use Firebase\JWT\JWT, Firebase\JWT\Key;

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
