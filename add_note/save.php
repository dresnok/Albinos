<?php
header('Content-Type: application/json');

$filepath = '../data/dane.json';

function backupJsonFile($filepath) {
    $backupDir = __DIR__ . '/trash/';
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0777, true);
    }

    $timestamp = date('Y-m-d_H-i-s');
    $backupName = "dane-{$timestamp}.json";
    $backupPath = $backupDir . $backupName;

    copy($filepath, $backupPath);
}


if (!file_exists($filepath)) {
    echo json_encode(['success' => false, 'error' => 'Plik JSON nie istnieje']);
    exit;
}

$data = json_decode(file_get_contents($filepath), true);
if (!is_array($data)) {
    echo json_encode(['success' => false, 'error' => 'Nieprawidłowy format JSON']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$tabId = $input['tab'] ?? null;
$itemId = $input['id'] ?? null;

if (!$tabId || !$itemId) {
    echo json_encode(['success' => false, 'error' => 'Brak wymaganych danych: tab lub id']);
    exit;
}


if (($input['action'] ?? '') === 'delete_note') {
	 backupJsonFile($filepath);
    $tabId = $input['tab'] ?? null;
    $itemId = $input['id'] ?? null;

    if (!$tabId || !$itemId) {
        echo json_encode(['success' => false, 'error' => 'Brak ID zakładki lub notatki']);
        exit;
    }

    foreach ($data as &$tab) {
        if ($tab['id'] === $tabId) {
            $before = count($tab['items'] ?? []);
            $tab['items'] = array_values(array_filter($tab['items'] ?? [], fn($item) => $item['id'] !== $itemId));
            $after = count($tab['items']);

            if ($before === $after) {
                echo json_encode(['success' => false, 'error' => 'Nie znaleziono notatki do usunięcia']);
                exit;
            }

            file_put_contents('debug.log', date('[Y-m-d H:i:s] ') . "Usunięto notatkę: {$itemId} z zakładki {$tabId}\n", FILE_APPEND);
            file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            echo json_encode(['success' => true]);
            exit;
        }
    }

    echo json_encode(['success' => false, 'error' => 'Zakładka nie istnieje']);
    exit;
}


function generateId($prefix = 'item', $base = '') {
    return $prefix . '-' . ($base ? $base . '-' : '') . substr(md5(uniqid('', true)), 0, 6);
}

$itemData = $input;
unset($itemData['tab']);

// 🔄 Etap 1: usuń duplikaty ID (z wyjątkiem pierwszego wystąpienia)
foreach ($data as &$tab) {
    if ($tab['id'] === $tabId && isset($tab['items']) && is_array($tab['items'])) {
        $seen = [];
        foreach ($tab['items'] as &$item) {
            if (!isset($seen[$item['id']])) {
                $seen[$item['id']] = true;
            } else {
                // To jest duplikat – nadaj nowy unikalny ID
                $item['id'] = generateId($tabId, $item['id']);
            }
        }
        unset($item);
        break;
    }
}
unset($tab);

// 🔄 Etap 2: aktualizacja lub dodanie nowego wpisu
$found = false;
foreach ($data as &$tab) {
    if ($tab['id'] === $tabId) {
        if (!isset($tab['items']) || !is_array($tab['items'])) {
            $tab['items'] = [];
        }

        foreach ($tab['items'] as &$item) {
            if ($item['id'] === $itemId) {
                $item = $itemData;
                $found = true;
                break;
            }
        }
        unset($item);

        if (!$found) {
            $tab['items'][] = $itemData;
            $found = true;
        }

        break;
    }
}



unset($tab);

if (!$found) {
    echo json_encode(['success' => false, 'error' => 'Nie znaleziono zakładki: ' . $tabId]);
    exit;
}

if (file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
    echo json_encode(['success' => true, 'id' => $itemData['id']]);
} else {
    echo json_encode(['success' => false, 'error' => 'Błąd zapisu JSON']);
}
?>
