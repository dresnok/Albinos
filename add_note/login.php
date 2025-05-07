<?php
session_start();

$data = json_decode(file_get_contents('php://input'), true);
$user = $data['username'] ?? '';
$pass = $data['password'] ?? '';

$configPath = __DIR__ . '/user/config.json';
if (!file_exists($configPath)) {
    echo json_encode(['success' => false, 'error' => 'Brak konfiguracji']);
    exit;
}

$config = json_decode(file_get_contents($configPath), true);
if ($config['username'] === $user && password_verify($pass, $config['password'])) {
    $_SESSION['user_logged_in'] = true;
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Nieprawidłowe dane logowania']);
}
