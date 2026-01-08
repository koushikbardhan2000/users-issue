<?php
// manager/profile_put.php
header('Content-Type: application/json');
require '../config.php';
// (JWT decode omitted; assume $userId)
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
