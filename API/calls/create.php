<?php
// calls/create.php
header('Content-Type: application/json');
require '../config.php';

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['problem_type'], $data['user_name'], $data['user_email'], $data['user_phone'], $data['description'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}
$sql = "INSERT INTO calls (problem_type, user_name, user_email, user_phone, description) 
        VALUES (?, ?, ?, ?, ?)";
$stmt = $pdo->prepare($sql);
try {
    // Use bound parameters to prevent SQL injection:contentReference[oaicite:3]{index=3}
    $stmt->execute([
        $data['problem_type'], 
        $data['user_name'], 
        $data['user_email'], 
        $data['user_phone'], 
        $data['description']
    ]);
    echo json_encode(['message' => 'Call created']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to create call']);
}
