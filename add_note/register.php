<?php
session_start();
$data = json_decode(file_get_contents('php://input'), true);
$user = trim($data['username'] ?? '');
$pass = trim($data['password'] ?? '');

if (!$user || !$pass) {
    echo json_encode(['success' => false, 'error' => 'Uzupełnij dane']);
    exit;
}

$configPath = __DIR__ . '/user/config.json';
if (file_exists($configPath)) {
    echo json_encode(['success' => false, 'error' => 'Konto już istnieje']);
    exit;
}

$hash = password_hash($pass, PASSWORD_BCRYPT);
file_put_contents($configPath, json_encode(['username' => $user, 'password' => $hash]));
echo json_encode(['success' => true]);
