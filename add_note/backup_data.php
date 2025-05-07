<?php
session_start();
if (!isset($_SESSION['user'])) {
  http_response_code(403);
  echo 'Brak dostępu';
  exit;
}

function log_event($message) {
    $logPath = __DIR__ . '/debug.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logPath, "[$timestamp] $message\n", FILE_APPEND);
}

function backup_json_if_needed($filepath, $backupDir = './arch', $maxBackups = 100) {
  if (!file_exists($filepath)) {
    error_log("❌ Plik nie istnieje: $filepath");
    return;
  }

  if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
  }

  // Wyszukaj istniejące backupy
  $files = glob($backupDir . '/dane_*.json');
  usort($files, function($a, $b) {
    return filemtime($a) - filemtime($b); // najstarszy pierwszy
  });

  // Jeśli za dużo backupów — usuń najstarsze
  while (count($files) >= $maxBackups) {
    $toDelete = array_shift($files);
    unlink($toDelete);
    error_log("🗑️ Usunięto stary backup: $toDelete");
  }

  // Zapisz nowy backup
  $timestamp = date('Y-m-d_H-i-s');
  $backupFile = rtrim($backupDir, '/') . '/dane_' . $timestamp . '.json';
  if (copy($filepath, $backupFile)) {
    error_log("✅ Backup zapisany: $backupFile");
  } else {
    error_log("⚠️ Nie udało się zrobić backupu!");
  }
}
log_event("Backup wykonany przez: " . $_SESSION['user']);

// Przykład użycia:
backup_json_if_needed('../data/dane.json', __DIR__ . '/arch');
