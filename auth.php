<?php
declare(strict_types=1);

ini_set('session.use_strict_mode', '1');

session_set_cookie_params([
    'httponly' => true,
    'secure' => !empty($_SERVER['HTTPS'])
        && $_SERVER['HTTPS'] !== 'off',
    'samesite' => 'Lax',
    'path' => '/'
]);

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}
