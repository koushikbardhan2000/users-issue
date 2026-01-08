<?php
// manager/support_engineers.php
header('Content-Type: application/json');
require '../config.php';
// (JWT decode, ensure role = MANAGER)
$stmt = $pdo->query("SELECT id, name, email, phone, status 
                     FROM users WHERE role = 'SUPPORT_ENGINEER'");
$engineers = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($engineers);
