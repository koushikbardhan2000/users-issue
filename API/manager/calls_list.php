<?php
// manager/calls_list.php
header('Content-Type: application/json');
require '../config.php';
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
