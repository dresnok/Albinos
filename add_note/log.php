<?php
$input = json_decode(file_get_contents('php://input'), true);
$msg = $input['msg'] ?? '(brak treści)';
file_put_contents('debug.log', date('[Y-m-d H:i:s] ') . $msg . "\n", FILE_APPEND);
echo json_encode(['success' => true]);
?>
