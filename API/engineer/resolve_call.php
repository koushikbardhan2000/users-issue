<?php
// engineer/resolve_call.php
header('Content-Type: application/json');
require '../config.php';
// (JWT decode, ensure role = SUPPORT_ENGINEER)
$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['call_id'], $data['resolution_status'], $data['issue_type'], $data['remarks'])) {
    http_response_code(400);
    echo json_encode(['error'=>'Missing parameters']);
    exit;
}
$callId = $data['call_id'];
// Verify call
$stmt = $pdo->prepare("SELECT status, engineer_id FROM calls WHERE id = ?");
$stmt->execute([$callId]);
$call = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$call) {
    http_response_code(404);
    echo json_encode(['error'=>'Call not found']);
    exit;
}
if ($call['status'] !== 'ONGOING' || $call['engineer_id'] != $userId) {
    http_response_code(403);
    echo json_encode(['error'=>'Not authorized or call not ongoing']);
    exit;
}
// Update resolution status (we could also record issue_type/remarks if schema is extended)
$stmt = $pdo->prepare("UPDATE calls SET resolution_status = ?, updated_at = NOW() WHERE id = ?");
try {
    $stmt->execute([$data['resolution_status'], $callId]);
    echo json_encode(['message'=>'Call resolution status updated']);
} catch (PDOException $e) {
    http_response_code(400);
    echo json_encode(['error'=>'Update failed']);
}
