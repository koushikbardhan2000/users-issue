<?php
// manager/call_details.php
header('Content-Type: application/json');
require '../config.php';
// (JWT decode)
$callId = $_GET['id'] ?? null;
if (!$callId) {
    http_response_code(400);
    echo json_encode(['error'=>'Missing call ID']);
    exit;
}
$stmt = $pdo->prepare("SELECT * FROM calls WHERE id = ?");
$stmt->execute([$callId]);
$call = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$call) {
    http_response_code(404);
    echo json_encode(['error'=>'Call not found']);
    exit;
}
echo json_encode($call);
