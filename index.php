<?php
declare(strict_types=1);

$search_term = '';
$error_message = '';

function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_POST['search_term']) || trim($_POST['search_term']) === '') {
        $error_message = 'Please enter a recipe name.';
    } else {
        $search_term = trim($_POST['search_term']);

        if (strlen($search_term) < 2) {
            $error_message = 'Search term must be at least 2 characters long.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cookbook recipe search</title>
</head>

<body>

    <h1>Cookbookery recipe search</h1>

    <form method="post" action="index.php">
        <label for="search_term">Recipe Name:</label>

        <input
            type="text"
            id="search_term"
            name="search_term"
            value="<?= e($search_term) ?>"
        >

        <button type="submit">Search</button>
    </form>

    <?php if ($error_message !== ''): ?>
        <p><?= e($error_message) ?></p>
    <?php endif; ?>

    <?php if ($error_message === '' && $search_term !== ''): ?>
        <h2>Search Results</h2>

        <p>
            You searched for:
            <strong><?= e($search_term) ?></strong>
        </p>
    <?php endif; ?>

</body>
</html>


