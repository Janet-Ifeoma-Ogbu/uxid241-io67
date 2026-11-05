<?php
declare(strict_types=1);

require_once __DIR__ . '/database.php';

ini_set('session.use_strict_mode', '1');

session_set_cookie_params([
    'httponly' => true,
    'secure' => !empty($_SERVER['HTTPS'])
        && $_SERVER['HTTPS'] !== 'off',
    'samesite' => 'Lax',
    'path' => '/'
]);

session_start();

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

if (isset($_SESSION['user_id'])) {
    header('Location: admin/index.php');
    exit;
}

$error_message = '';
$email = '';

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $csrf_token = (string) ($_POST['csrf_token'] ?? '');

    if (!hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        $error_message = 'Invalid request. Please try again.';
    } elseif ($email === '' || $password === '') {
        $error_message = 'Please enter your email and password.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        $result = mysqli_execute_query(
            $mysqli,
            'SELECT id, name, email, password_hash
             FROM users
             WHERE email = ?
             LIMIT 1',
            [$email]
        );

        $user = mysqli_fetch_assoc($result);
        mysqli_free_result($result);

        if (
            $user !== null
            && password_verify($password, (string) $user['password_hash'])
        ) {
            session_regenerate_id(true);

            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['user_name'] = (string) $user['name'];
            $_SESSION['user_email'] = (string) $user['email'];

            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            header('Location: admin/index.php');
            exit;
        }

        $error_message = 'Incorrect email or password.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Login | Cookbookery</title>
    
</head>
<body>
    <main class="login-page">
        <section class="login-card">
            <a class="brand" href="index.php">
                <span class="brand-mark" aria-hidden="true">c</span>
                <span>cookbookery</span>
            </a>

            <h1>Welcome back</h1>
            <p>Log in to access your Cookbookery account.</p>

            <?php if ($error_message !== ''): ?>
                <p class="form-message" role="alert">
                    <?= e($error_message) ?>
                </p>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e($_SESSION['csrf_token']) ?>"
                >

                <label for="email">Email address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= e($email) ?>"
                    autocomplete="username"
                    required
                >

                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    autocomplete="current-password"
                    required
                >

                <button type="submit">Log in</button>
            </form>

            <p><a href="index.php">Back to recipes</a></p>
        </section>
    </main>
    <style>
        
.login-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    background: #f7f7f2;
}

.login-card {
    width: 100%;
    max-width: 430px;
    padding: 42px;
    background: white;
    border-radius: 18px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
}

.login-card h1 {
    color: #263b2b;
    font-size:50px;
}

.login-card button {
    width: 100%;
    padding: 15px;
    background: #355b3c;
    color: white;
    border: none;
    border-radius: 9px;
    cursor: pointer;
}

.login-card input {
    width: 100%;
    box-sizing: border-box;
    padding: 13px;
    border: 1px solid #deded7;
    border-radius: 8px;
}

    </style>
</body>
</html>
