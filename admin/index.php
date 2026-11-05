<?php
declare(strict_types=1);

require_once __DIR__ . '/../auth.php';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Cookbookery</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <main class="admin-page">
        <section class="admin-card">
            <h1>Welcome to Cookbookery!</h1>

            <p>
                Hello, <?= e((string) $_SESSION['user_name']) ?>!
            </p>

            <p>You are successfully logged in.</p>

            <a href="../index.php">View Recipes</a>

            <form method="POST" action="../logout.php">
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e((string) $_SESSION['csrf_token']) ?>"
                >

                <button type="submit">Log Out</button>
            </form>
        </section>
    </main>
    <style>
/* ADMIN DASHBOARD */

.admin-page {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 30px;
    background: #f7f7f2;
}

.admin-card {
    width: 100%;
    max-width: 550px;
    padding: 40px;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.07);
}

.admin-card h1 {
    color: #263b2b;
}

.admin-card p {
    color: #666;
    line-height: 1.6;
}

.admin-card a {
    display: inline-block;
    margin: 15px 0;
    color: #355b3c;
    font-weight: 600;
}

.admin-card button {
    display: block;
    padding: 12px 25px;
    border: none;
    border-radius: 8px;
    background: #355b3c;
    color: white;
    cursor: pointer;
}
</style>
</body>
</html>
