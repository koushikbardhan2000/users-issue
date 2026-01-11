<?php
require '../config.php';
require '../../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header('Content-Type: application/json');

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

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Fetch all engineers
    $stmt = $pdo->query("SELECT id, name, email, phone, status FROM users WHERE role = 'SUPPORT_ENGINEER'");
    echo json_encode(['status' => 'success', 'engineers' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);

} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!isset($data['name'], $data['email'], $data['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Name, email, and password required']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, 'SUPPORT_ENGINEER')");
    try {
        $stmt->execute([$data['name'], $data['email'], $data['phone'] ?? '', $data['password']]);
        echo json_encode(['message' => 'Support engineer added']);
    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(['error' => 'Email already exists or invalid data']);
    }

} elseif ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Engineer ID required']);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, status = ? WHERE id = ? AND role = 'SUPPORT_ENGINEER'");
    $stmt->execute([
        $data['name'] ?? '',
        $data['email'] ?? '',
        $data['phone'] ?? '',
        $data['status'] ?? 'ACTIVE',
        $data['id']
    ]);
    echo json_encode(['message' => 'Engineer updated']);

} elseif ($method === 'DELETE') {
    parse_str(file_get_contents("php://input"), $data);
    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Engineer ID required']);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'SUPPORT_ENGINEER'");
    $stmt->execute([$data['id']]);
    echo json_encode(['message' => 'Engineer deleted']);
}
