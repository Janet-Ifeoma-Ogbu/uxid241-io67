<?php
declare(strict_types=1);

require_once __DIR__ . '/database.php';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$search_term = '';
$error_message = '';
$search_pattern = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search_term = trim((string) ($_POST['search_term'] ?? ''));

    if ($search_term === '') {
        $error_message = 'Please enter a recipe name.';
    } elseif (strlen($search_term) < 2) {
        $error_message = 'Enter at least 2 characters.';
    } else {
        $search_pattern = '%' . $search_term . '%';
    }
}

if ($search_pattern !== '') {
    $result = mysqli_execute_query(
        $mysqli,
        'SELECT id, title, description, ingredients,
                instructions, cooking_time_minutes
         FROM recipes
         WHERE title LIKE ?
         ORDER BY title ASC',
        [$search_pattern]
    );
} else {
    $result = mysqli_query(
        $mysqli,
        'SELECT id, title, description, ingredients,
                instructions, cooking_time_minutes
         FROM recipes
         ORDER BY title ASC'
    );
}

$recipes = [];
while ($row = mysqli_fetch_assoc($result)) {
    $recipes[] = $row;
}
mysqli_free_result($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Find something delicious to make with Cookbookery.">
    <link rel="stylesheet" href="style.css ">
    <title>Cookbookery — Find your next favorite</title>
    
</head>
<body>
    <header class="site-header">
        <div class="header-inner">
            <a class="brand" href="index.php" aria-label="Cookbookery home">
                <span class="brand-mark" aria-hidden="true">c</span>
                <span>cookbookery</span>
            </a>
            <span class="header-note">Good food starts here</span>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="hero-copy">
                <p class="eyebrow">YOUR EVERYDAY RECIPE BOOK</p>
                <h1>What sounds<br><span>good today?</span></h1>
                <p class="hero-description">Find a new favorite, get inspired, and make something delicious at home.</p>

                <form class="search-form" method="POST" action="index.php">
                    <label class="sr-only" for="search_term">Search recipes by name</label>
                    <span class="search-icon" aria-hidden="true">⌕</span>
                    <input
                        id="search_term"
                        name="search_term"
                        type="search"
                        placeholder="Search recipes by name"
                        value="<?= e($search_term) ?>"
                    >
                    <button type="submit">Search</button>
                </form>
                <?php if ($error_message !== ''): ?>
                    <p class="form-message" role="alert"><?= e($error_message) ?></p>
                <?php endif; ?>
            </div>
            <div class="hero-art" aria-hidden="true">
                <div class="art-circle circle-one"></div>
                <div class="art-circle circle-two"></div>
                <div class="art-card">
                    <span class="art-spark">✳</span>
                    <span class="art-card-label">MADE WITH LOVE</span>
                    <span class="art-plate">◉</span>
                    <span class="art-card-footer">Simple ideas. Delicious meals.</span>
                </div>
            </div>
        </section>

        <section class="recipe-section" id="recipes">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">THE RECIPE COLLECTION</p>
                    <h2><?= $search_pattern !== '' ? 'Search results' : 'Recipes to love' ?></h2>
                    <p class="section-description">
                        <?php if ($search_pattern !== ''): ?>
                            Showing matches for “<?= e($search_term) ?>”.
                        <?php else: ?>
                            Explore the collection and find your next kitchen favorite.
                        <?php endif; ?>
                    </p>
                </div>
                <span class="result-count"><?= count($recipes) ?> <?= count($recipes) === 1 ? 'recipe' : 'recipes' ?></span>
            </div>

            <?php if (count($recipes) === 0): ?>
                <div class="empty-state">
                    <span class="empty-icon" aria-hidden="true">⌕</span>
                    <h3>No recipes found</h3>
                    <p>Try another search, or browse the full collection.</p>
                    <a class="button-secondary" href="index.php">View all recipes</a>
                </div>
            <?php else: ?>
                <div class="recipe-grid">
                    <?php foreach ($recipes as $index => $recipe): ?>
                        <?php
                        $ingredient_lines = array_filter(
                            array_map('trim', explode("\n", (string) $recipe['ingredients'])),
                            static fn(string $line): bool => $line !== ''
                        );
                        $instruction_lines = array_filter(
                            array_map('trim', explode("\n", (string) $recipe['instructions'])),
                            static fn(string $line): bool => $line !== ''
                        );
                        ?>
                        <article class="recipe-card">
                            <div class="recipe-visual visual-<?= ($index % 4) + 1 ?>">
                                <span class="recipe-number">RECIPE <?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
                                <span class="visual-symbol" aria-hidden="true"><?= ['✳', '◒', '✿', '◉'][$index % 4] ?></span>
                                <span class="visual-caption">A little inspiration<br>for your table</span>
                            </div>
                            <div class="recipe-content">
                                <div class="recipe-meta">
                                    <span class="time-pill"><?= (int) $recipe['cooking_time_minutes'] ?> min <span aria-hidden="true">·</span> est.</span>
                                    <span class="fresh-label">COOK SOMETHING GOOD</span>
                                </div>
                                <h3><?= e((string) $recipe['title']) ?></h3>
                                <p class="recipe-description"><?= e((string) $recipe['description']) ?></p>

                                <details class="recipe-details">
                                    <summary>View recipe <span class="summary-plus" aria-hidden="true">+</span></summary>
                                    <div class="details-content">
                                        <h4>Ingredients</h4>
                                        <?php if (count($ingredient_lines) > 0): ?>
                                            <ul class="ingredient-list">
                                                <?php foreach ($ingredient_lines as $ingredient): ?>
                                                    <li><?= e($ingredient) ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            <p>Ingredient information is not available.</p>
                                        <?php endif; ?>

                                        <h4>Instructions</h4>
                                        <?php if (count($instruction_lines) > 0): ?>
                                            <ol class="instruction-list">
                                                <?php foreach ($instruction_lines as $instruction): ?>
                                                    <li><?= e(preg_replace('/^\d+\.\s*/', '', $instruction) ?? $instruction) ?></li>
                                                <?php endforeach; ?>
                                            </ol>
                                        <?php else: ?>
                                            <p>Instruction information is not available.</p>
                                        <?php endif; ?>
                                    </div>
                                </details>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer class="site-footer">
        <a class="brand footer-brand" href="index.php">
            <span class="brand-mark" aria-hidden="true">c</span>
            <span>cookbookery</span>
        </a>
        <p>Made for curious cooks and good meals.</p>
    </footer>
</body>
</html>

