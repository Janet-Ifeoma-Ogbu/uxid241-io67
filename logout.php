<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$csrf_token = (string) ($_POST['csrf_token'] ?? '');

if (
    !isset($_SESSION['csrf_token'])
    || !hash_equals($_SESSION['csrf_token'], $csrf_token)
) {
    http_response_code(403);
    exit('Invalid request.');
}

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $cookie_params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        [
            'expires' => time() - 42000,
            'path' => $cookie_params['path'],
            'domain' => $cookie_params['domain'],
            'secure' => $cookie_params['secure'],
            'httponly' => $cookie_params['httponly'],
            'samesite' => 'Lax'
        ]
    );
}

session_destroy();

header('Location: login.php');
exit;
