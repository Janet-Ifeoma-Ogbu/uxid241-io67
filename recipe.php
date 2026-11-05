<?php
declare(strict_types=1);

require_once __DIR__ . '/database.php';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$recipe_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$recipe_id || $recipe_id < 1) {
    http_response_code(404);
    exit('Recipe not found.');
}

$result = mysqli_execute_query(
    $mysqli,
    'SELECT * FROM recipes WHERE id = ?',
    [$recipe_id]
);

$recipe = mysqli_fetch_assoc($result);
mysqli_free_result($result);

if (!$recipe) {
    http_response_code(404);
    exit('Recipe not found.');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($recipe['title']) ?> | Cookbookery</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <main class="recipe-detail">

        <a href="index.php" class="back-link">
            ← Back to recipes
        </a>

        <article class="recipe-content">

            <h1><?= e($recipe['title']) ?></h1>

            <p class="recipe-description">
                <?= e($recipe['description']) ?>
            </p>

            <p>
                <strong>Cooking Time:</strong>
                <?= e((string) $recipe['cooking_time_minutes']) ?> minutes
            </p>

            <section>
                <h2>Ingredients</h2>
                <div>
                    <?= nl2br(e($recipe['ingredients'])) ?>
                </div>
            </section>

            <section>
                <h2>Instructions</h2>
                <div>
                    <?= nl2br(e($recipe['instructions'])) ?>
                </div>
            </section>

        </article>

    </main>

    <style>
/* RECIPE DETAIL PAGE */

.recipe-detail {
    max-width: 900px;
    margin: 0 auto;
    padding: 50px 25px;
}

.back-link {
    display: inline-block;
    margin-bottom: 30px;
    color: #355b3c;
    text-decoration: none;
    font-weight: 600;
}

.recipe-content {
    padding: 40px;
    background: #fff;
    border-radius: 16px;
}

.recipe-content h1 {
    color: #263b2b;
    font-size: 38px;
    margin-bottom: 15px;
}

.recipe-description {
    color: #666;
    line-height: 1.7;
}

.recipe-content section {
    margin-top: 35px;
}

.recipe-content h2 {
    color: #263b2b;
    margin-bottom: 15px;
}

@media (max-width: 600px) {
    .recipe-content {
        padding: 24px;
    }

    .recipe-content h1 {
        font-size: 28px;
    }
}
</style>
</body>
</html>
