<?php
header('Content-Type: application/json');

$filepath = '../data/dane.json';

// Odczytaj dane
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
$action = $input['action'] ?? '';


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


if ($action === 'rename' && !empty($input['oldId']) && !empty($input['newLabel'])) {
    $oldId = $input['oldId'];
    $newLabel = trim($input['newLabel']);

    // znajdź zakładkę po ID i zmień tylko label
    foreach ($data as &$tab) {
        if ($tab['id'] === $oldId) {
            if ($tab['label'] === $newLabel) {
                echo json_encode(['success' => false, 'error' => 'Nowa nazwa jest taka sama jak poprzednia.']);
                exit;
            }

            $tab['label'] = $newLabel;
            file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            echo json_encode(['success' => true]);
            exit;
        }
    }
file_put_contents('debug.log', date('[Y-m-d H:i:s] ') . "Zmieniono nazwę zakładki: {$oldId} -> {$newLabel}\n", FILE_APPEND);

    echo json_encode(['success' => false, 'error' => 'Zakładka nie istnieje']);
    exit;
}
elseif ($action === 'add' && !empty($input['label'])) {
    $label = trim($input['label']);

    // wygeneruj unikalne id (np. tab-xxxxxx)
    $newId = 'tab-' . substr(md5(uniqid('', true)), 0, 6);

    $data[] = [
        'id' => $newId,
        'label' => $label,
        'items' => []
    ];
file_put_contents('debug.log', date('[Y-m-d H:i:s] ') . "Dodano zakładkę: {$label} (ID: {$newId})\n", FILE_APPEND);

    file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo json_encode(['success' => true, 'id' => $newId]);
    exit;

} elseif ($action === 'delete' && !empty($input['tab'])) {
    $tabId = trim($input['tab']);

    backupJsonFile($filepath);

    // znajdź zakładkę po ID, żeby pobrać label przed usunięciem
    $label = '';
    foreach ($data as $tab) {
        if ($tab['id'] === $tabId) {
            $label = $tab['label'];
            break;
        }
    }

    // filtruj i usuń zakładkę po ID
    $found = false;
    $data = array_filter($data, function ($t) use ($tabId, &$found) {
        if ($t['id'] === $tabId) {
            $found = true;
            return false; // usuń ten wpis
        }
        return true; // zostaw pozostałe
    });

    if (!$found) {
        echo json_encode(['success' => false, 'error' => 'Zakładka nie istnieje']);
        exit;
    }

    // spójny komunikat logowania
    file_put_contents('debug.log', date('[Y-m-d H:i:s] ') . "Usunięto zakładkę: {$label} (ID: {$tabId})\n", FILE_APPEND);

    // zapis danych
    file_put_contents($filepath, json_encode(array_values($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo json_encode(['success' => true]);
    exit;
}

elseif ($action === 'reorder' && is_array($input['tabs'])) {
    $newOrder = $input['tabs']; // array of IDs

    $reordered = [];
    foreach ($newOrder as $id) {
        foreach ($data as $tab) {
            if ($tab['id'] === $id) {
                $reordered[] = $tab;
                break;
            }
        }
    }

    if (count($reordered) === count($data)) {
		file_put_contents('debug.log', date('[Y-m-d H:i:s] ') . "Zmieniono kolejność zakładek: " . implode(', ', $newOrder) . "\n", FILE_APPEND);


        $data = $reordered;
        file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo json_encode(['success' => true]);
        exit;
    } else {
        echo json_encode(['success' => false, 'error' => 'Nieprawidłowa liczba zakładek']);
        exit;
    }
}



?>
