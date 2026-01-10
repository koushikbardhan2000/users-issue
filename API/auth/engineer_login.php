<?php
require '../config.php';
require '../../vendor/autoload.php';
use Firebase\JWT\JWT;

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
if (empty($data['email']) || empty($data['password'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Email and password required']);
    exit;
}

$stmt = $pdo->prepare("SELECT id, password, role FROM users WHERE email = ?");
$stmt->execute([$data['email']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['role'] !== 'SUPPORT_ENGINEER') {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials (not a support engineer)']);
    exit;
}

if ($user['password'] !== $data['password']) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials (wrong password)']);
    exit;
}

$issuedAt = time();
$expire = $issuedAt + 3600;
$payload = [
    'iat' => $issuedAt,
    'exp' => $expire,
    'data' => [
        'user_id' => $user['id'],
        'role' => $user['role']
    ]
];

$jwt = JWT::encode($payload, $jwt_secret, 'HS256');
echo json_encode([
    'status' => 'success',
    'token' => $jwt,
    'message' => 'Engineer login successful'
]);
