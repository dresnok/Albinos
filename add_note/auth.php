<?php
session_start();
header('Content-Type: application/json');


function log_event($message) {
    $logPath = __DIR__ . '/debug.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logPath, "[$timestamp] $message\n", FILE_APPEND);
}


$usersFile = __DIR__ . '/user/config.json';
$method = $_SERVER['REQUEST_METHOD'];

// Dla żądań POST
if ($method === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    $action = $input['action'] ?? '';

    // Wylogowanie
    if ($action === 'logout') {
		log_event("Wylogowanie: " . ($_SESSION['user'] ?? 'nieznany'));

        session_destroy();
        echo json_encode(['success' => true]);
        exit;
    }

    // Rejestracja
if ($action === 'register') {
	
	    $input = json_decode(file_get_contents('php://input'), true);
$rootPasswordInput = $input['rootPassword'] ?? '';

$configPath = __DIR__ . '/user/configx.json';
if (!file_exists($configPath)) {
    echo json_encode(['success' => false, 'error' => 'Brak pliku konfiguracyjnego.']);
    exit;
}

$config = json_decode(file_get_contents($configPath), true);
$expectedPassword = $config['rootPassword'] ?? '';


if (!password_verify($rootPasswordInput, $expectedPassword)) {
    echo json_encode(['success' => false, 'error' => 'Niepoprawne hasło dostępu.']);
    exit;
}

	
    $username = trim($input['username'] ?? '');
    $passwordRaw = trim($input['password'] ?? '');

    // Walidacja loginu
    if (strlen($username) < 3 || strlen($username) > 20 || !preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        echo json_encode(['success' => false, 'error' => 'Nazwa użytkownika musi mieć 3-20 znaków i zawierać tylko litery, cyfry lub _']);
        exit;
    }

    // Walidacja hasła
    if (strlen($passwordRaw) < 4 || strlen($passwordRaw) > 50) {
        echo json_encode(['success' => false, 'error' => 'Hasło musi mieć od 4 do 50 znaków']);
        exit;
    }

    $password = password_hash($passwordRaw, PASSWORD_DEFAULT);
    $createdAt = date('Y-m-d H:i:s');

    $users = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : ['users' => []];

    // sprawdź czy użytkownik już istnieje
    foreach ($users['users'] as $u) {
        if ($u['username'] === $username) {
            echo json_encode(['success' => false, 'error' => 'Użytkownik już istnieje']);
            exit;
        }
    }

    $users['users'][] = [
        'username' => $username,
        'password' => $password,
        'created_at' => $createdAt
    ];

    file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
    $_SESSION['user'] = $username;
				log_event("Rejestracja: $username");


    $allUsers = array_map(fn($user) => [
        'username' => $user['username'],
        'created_at' => $user['created_at']
    ], $users['users']);

    echo json_encode([
        'success' => true,
        'created_at' => $createdAt,
        'all_users' => $allUsers
    ]);
    exit;
}



    // Logowanie
    if ($action === 'login') {
        if (!file_exists($usersFile)) {
            echo json_encode(['success' => false, 'error' => 'Brak bazy użytkowników']);
            exit;
        }

        $users = json_decode(file_get_contents($usersFile), true);
        $username = trim($input['username']);
        $password = trim($input['password']);

        foreach ($users['users'] as $u) {
            if ($u['username'] === $username && password_verify($password, $u['password'])) {
                $_SESSION['user'] = $username;

log_event("Logowanie: $username");

                echo json_encode([
                    'success' => true,
                    'created_at' => $u['created_at'],
                    'all_users' => array_map(fn($user) => [
                        'username' => $user['username'],
                        'created_at' => $user['created_at']
                    ], $users['users'])
                ]);
                exit;
            }
        }

        echo json_encode(['success' => false, 'error' => 'Nieprawidłowe dane']);
        exit;
    }

    // Zmiana hasła
    if ($action === 'change_password') {
		log_event("Zmiana hasła: " . $_SESSION['user']);

        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'error' => 'Brak dostępu']);
            exit;
        }

        $users = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : ['users' => []];
        $found = false;

        foreach ($users['users'] as &$u) {
            if ($u['username'] === $_SESSION['user']) {
                $u['password'] = password_hash(trim($input['password']), PASSWORD_DEFAULT);
                $found = true;
                break;
            }
        }

        if ($found) {
            file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Użytkownik nie znaleziony']);
        }
        exit;
    }

    // Usunięcie konta
    if ($action === 'delete_account') {
		log_event("Konto usunięte: " . $_SESSION['user']);

        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'error' => 'Brak dostępu']);
            exit;
        }

        $users = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : ['users' => []];
        $before = count($users['users']);

        $users['users'] = array_filter($users['users'], fn($u) => $u['username'] !== $_SESSION['user']);
        $after = count($users['users']);

        if ($before === $after) {
            echo json_encode(['success' => false, 'error' => 'Użytkownik nie znaleziony']);
            exit;
        }

        $users['users'] = array_values($users['users']); // resetuj indeksy
        file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
        session_destroy();

        echo json_encode(['success' => true]);
        exit;
    }

    // Nieznana akcja
    echo json_encode(['success' => false, 'error' => 'Nieznana akcja']);
    exit;
}

// Dla GET – sprawdzanie statusu sesji
if ($method === 'GET') {
    $usersFile = __DIR__ . '/user/config.json';

    if (!file_exists($usersFile)) {
        echo json_encode(['status' => 'no_users']);
        exit;
    }

    $users = json_decode(file_get_contents($usersFile), true);
    if (empty($users['users'])) {
        echo json_encode(['status' => 'no_users']);
        exit;
    }

    if (isset($_SESSION['user'])) {
        // przygotuj dane o userze
        $currentUser = null;
        $userList = [];

        foreach ($users['users'] as $u) {
            if ($u['username'] === $_SESSION['user']) {
                $currentUser = $u;
            }
            $userList[] = [
                'username' => $u['username'],
                'created_at' => $u['created_at']
            ];
        }

        echo json_encode([
            'status' => 'ok',
            'user' => $_SESSION['user'],
            'created_at' => $currentUser['created_at'] ?? null,
            'all_users' => $userList
        ]);
        exit;
    }

    // 🔴 Użytkownicy istnieją, ale nikt nie jest zalogowany
    echo json_encode(['status' => 'login_required']);
    exit;
}



// Jeżeli żadna metoda nie pasuje
http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
exit;
