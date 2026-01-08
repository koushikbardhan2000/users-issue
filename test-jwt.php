<?php
require 'vendor/autoload.php';
use Firebase\JWT\JWT;

$secretKey = 'b92c840f9a27e847a91ebf78aa14c6df';
$payload = [
    'iss' => 'localhost',
    'iat' => time(),
    'exp' => time() + 3600,
    'data' => ['user_id' => 1]
];

$jwt = JWT::encode($payload, $secretKey, 'HS256');
echo "Generated JWT: " . $jwt;
