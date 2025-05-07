<?php
session_start();
$mode = $_GET['mode'] ?? '';

function log_event($message) {
    $logPath = __DIR__ . '/debug.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logPath, "[$timestamp] $message\n", FILE_APPEND);
}

// 🔄 Lista plików w koszu
if ($mode === 'trashlist') {
    $trashDir = __DIR__ . '/trash/';
    $files = is_dir($trashDir) ? array_values(array_diff(scandir($trashDir), ['.', '..'])) : [];
    echo json_encode(['files' => $files]);
    exit;
}

// 🧨 Opróżnienie kosza
if ($mode === 'trashclear') {
    $trashDir = __DIR__ . '/trash/';
    $deleted = 0;

    if (is_dir($trashDir)) {
        foreach (scandir($trashDir) as $file) {
            if ($file !== '.' && $file !== '..') {
                $fullPath = $trashDir . '/' . $file;
                if (is_file($fullPath)) {
                    unlink($fullPath);
                    $deleted++;
                }
            }
        }
    }

    if (isset($_SESSION['user'])) {
        log_event("Opróżniono kosz – usunięto $deleted plików");
    }

    echo json_encode(['success' => true, 'deleted' => $deleted]);
    exit;
}

// 📂 Lista plików archiwum
if ($mode === 'archive') {
    $backupDir = './arch/';
    $maxBackups = 10;

    $files = array_values(array_filter(scandir($backupDir), function($file) use ($backupDir) {
        return is_file($backupDir . $file) && preg_match('/^dane_.*\.json$/', $file);
    }));

    sort($files);

    echo json_encode([
        'files' => $files,
        'max' => $maxBackups,
        'count' => count($files)
    ]);
    exit;
}

// 🧹 Wyczyść debug.log
if ($mode === 'clearlog') {
    file_put_contents('debug.log', '');
    file_put_contents('debug.log', date('[Y-m-d H:i:s] ') . "🧹 Wyczyszczono debug.log z poziomu panelu\n", FILE_APPEND);
    echo json_encode(['success' => true, 'message' => 'Log wyczyszczony']);
    exit;
}

// 🗑️ Usuń pojedynczy plik archiwum
if ($mode === 'delete' && isset($_GET['file'])) {
    $file = basename($_GET['file']);
    $path = './arch/' . $file;

    if (is_file($path)) {
        unlink($path);
        log_event("Usunięto plik z archiwum: $file");
        echo json_encode(['success' => true, 'message' => "Usunięto $file"]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Plik nie istnieje']);
    }
    exit;
}

// 🧨 Wyczyść całe archiwum
if ($mode === 'cleararch') {
    $sourceDir = './arch/';
    $trashDir = './trash/';
    $moved = 0;

    if (!is_dir($trashDir)) {
        mkdir($trashDir, 0777, true);
    }

    foreach (glob($sourceDir . 'dane_*.json') as $file) {
        if (is_file($file)) {
            $targetPath = $trashDir . basename($file);
            if (rename($file, $targetPath)) {
                $moved++;
            }
        }
    }

    log_event("📦 Przeniesiono $moved plików z archiwum do kosza");
    echo json_encode(['success' => true, 'deleted' => $moved]);
    exit;
}


// 🟥 Nieobsługiwany tryb
http_response_code(400);
echo json_encode(['error' => 'Nieprawidłowy tryb']);
