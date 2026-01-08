// manager/login.php (similar for engineer)
// Receive JSON { "email": "...", "password": "..." }
require '../config.php';
require '../../vendor/autoload.php';
use Firebase\JWT\JWT;

$data = json_decode(file_get_contents("php://input"), true);
if (empty($data['email']) || empty($data['password'])) {
    http_response_code(400);
    echo json_encode(['error'=>'Email and password required']);
    exit;
}
// Fetch user by email and role
$stmt = $pdo->prepare("SELECT id, password, role FROM users WHERE email = ?");
$stmt->execute([$data['email']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user || $user['role'] !== 'MANAGER' /* or 'SUPPORT_ENGINEER' */) {
    http_response_code(401);
    echo json_encode(['error'=>'Invalid credentials']);
    exit;
}
// (In production, use password_hash()/password_verify(); here we assume plaintext for simplicity)
if ($user['password'] !== $data['password']) {
    http_response_code(401);
    echo json_encode(['error'=>'Invalid credentials']);
    exit;
}
// Build JWT payload
$issuedAt = time();
$expire   = $issuedAt + 3600;  // 1 hour expiration
$payload = [
    'iat'  => $issuedAt,
    'exp'  => $expire,
    'data' => [
        'user_id' => $user['id'],
        'role'    => $user['role']
    ]
];
$jwt = JWT::encode($payload, $jwt_secret);
echo json_encode(['token' => $jwt]);
