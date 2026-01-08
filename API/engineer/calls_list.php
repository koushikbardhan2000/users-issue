<?php
// engineer/calls_list.php
header('Content-Type: application/json');
require '../config.php';
// (JWT decode, ensure role = SUPPORT_ENGINEER)
$status = $_GET['status'] ?? '';
if ($status !== 'ONGOING' && $status !== 'CLOSED') {
    http_response_code(400);
    echo json_encode(['error'=>'Invalid status']);
    exit;
}
$sql = "SELECT * FROM calls WHERE status = ? AND engineer_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$status, $userId]);
$calls = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($calls);
