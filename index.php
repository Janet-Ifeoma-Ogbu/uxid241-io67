<?php
declare(strict_types=1);

require_once __DIR__ . '/database.php';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// Filter values
$search_term = trim((string) ($_GET['search_term'] ?? ''));
$category = trim((string) ($_GET['category'] ?? ''));
$ingredient = trim((string) ($_GET['ingredient'] ?? ''));

$error_message = '';

$allowed_categories = [
    'Breakfast',
    'Lunch',
    'Dinner',
    'Dessert',
    'Snacks'
];

$allowed_times = [15, 30, 60];

$max_time_input = filter_input(
    INPUT_GET,
    'max_time',
    FILTER_VALIDATE_INT
);

$max_time = in_array($max_time_input, $allowed_times, true)
    ? $max_time_input
    : 0;

// Validate filters
if ($category !== '' && !in_array($category, $allowed_categories, true)) {
    $category = '';
}

if ($search_term !== '' && strlen($search_term) < 2) {
    $error_message = 'Enter at least 2 characters to search.';
}

if ($ingredient !== '' && strlen($ingredient) < 2) {
    $error_message = 'Enter at least 2 characters for an ingredient.';
}

// Build the recipe query
$sql = 'SELECT id, title, description, ingredients,
               instructions, cooking_time_minutes, category
        FROM recipes
        WHERE 1=1';

$params = [];

// Search by recipe name or description
if ($search_term !== '' && strlen($search_term) >= 2) {
    $sql .= ' AND (title LIKE ? OR description LIKE ?)';

    $search_pattern = '%' . $search_term . '%';

    $params[] = $search_pattern;
    $params[] = $search_pattern;
}

// Filter by category
if ($category !== '') {
    $sql .= ' AND category = ?';
    $params[] = $category;
}

// Filter by cooking time
if ($max_time > 0) {
    $sql .= ' AND cooking_time_minutes <= ?';
    $params[] = $max_time;
}

// Search by ingredient
if ($ingredient !== '' && strlen($ingredient) >= 2) {
    $sql .= ' AND ingredients LIKE ?';
    $params[] = '%' . $ingredient . '%';
}

$sql .= ' ORDER BY title ASC';

$result = mysqli_execute_query($mysqli, $sql, $params);

$recipes = [];

while ($row = mysqli_fetch_assoc($result)) {
    $recipes[] = $row;
}

mysqli_free_result($result);

// Check whether filters are active
$filters_active = (
    $search_term !== '' ||
    $category !== '' ||
    $max_time > 0 ||
    $ingredient !== ''
);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description" content="Find something delicious to make with Cookbookery.">

    <link rel="stylesheet" href="style.css">

    <title>Cookbookery — Find your next favorite</title>
</head>

<body>

    <header class="site-header">

        <div class="header-inner">

            <a class="brand" href="index.php" aria-label="Cookbookery home">
                <span class="brand-mark" aria-hidden="true">c</span>
                <span>cookbookery</span>
            </a>

            <a class="header-note" href="login.php">Admin Login</a>

        </div>

    </header>

    <main>

        <section class="hero">

            <div class="hero-copy">

                <p class="eyebrow">YOUR EVERYDAY RECIPE BOOK</p>

                <h1>What sounds<br><span>good today?</span></h1>

                <p class="hero-description">
                    Find a new favorite, get inspired, and make something delicious at home.
                </p>

                <?php if ($error_message !== ''): ?>
                    <p class="form-message" role="alert">
                        <?= e($error_message) ?>
                    </p>
                <?php endif; ?>

                <!-- SEARCH AND FILTER FORM -->

                <form class="filter-form" method="GET" action="index.php">

                    <div class="search-form">

                        <label class="sr-only" for="search_term">
                            Search recipes by name
                        </label>

                        <span class="search-icon" aria-hidden="true">⌕</span>

                        <input
                            id="search_term"
                            name="search_term"
                            type="search"
                            placeholder="Search recipes by name"
                            value="<?= e($search_term) ?>"
                        >

                        <button type="submit">Search</button>

                    </div>

                    <div class="filter-options">

                        <div class="filter-group">

                            <label for="category">Category</label>

                            <select id="category" name="category">

                                <option value="">All Categories</option>

                                <?php foreach ($allowed_categories as $option): ?>

                                    <option
                                        value="<?= e($option) ?>"
                                        <?= $category === $option ? 'selected' : '' ?>
                                    >
                                        <?= e($option) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <div class="filter-group">

                            <label for="max_time">Cooking Time</label>

                            <select id="max_time" name="max_time">

                                <option value="">Any Time</option>

                                <option value="15" <?= $max_time === 15 ? 'selected' : '' ?>>
                                    Under 15 minutes
                                </option>

                                <option value="30" <?= $max_time === 30 ? 'selected' : '' ?>>
                                    Under 30 minutes
                                </option>

                                <option value="60" <?= $max_time === 60 ? 'selected' : '' ?>>
                                    Under 60 minutes
                                </option>

                            </select>

                        </div>

                        <div class="filter-group">

                            <label for="ingredient">Ingredient</label>

                            <input
                                type="text"
                                id="ingredient"
                                name="ingredient"
                                placeholder="e.g. chicken"
                                value="<?= e($ingredient) ?>"
                            >

                        </div>

                    </div>

                    <div class="filter-actions">

                        <button type="submit" class="apply-filters-btn">
                            Apply Filters
                        </button>

                        <a href="index.php" class="clear-filters">
                            Clear Filters
                        </a>

                    </div>

                </form>

            </div>

        </section>

        <section class="recipe-section" id="recipes">

            <div class="section-heading">

                <div>

                    <p class="eyebrow">THE RECIPE COLLECTION</p>

                    <h2>
                        <?= $filters_active ? 'Search results' : 'Recipes to love' ?>
                    </h2>

                    <p class="section-description">

                        <?php if ($filters_active): ?>

                            Here are the recipes matching your filters.

                        <?php else: ?>

                            Explore the collection and find your next kitchen favorite.

                        <?php endif; ?>

                    </p>

                </div>

                <span class="result-count">
                    <?= count($recipes) ?>
                    <?= count($recipes) === 1 ? 'recipe' : 'recipes' ?>
                </span>

            </div>

            <?php if (count($recipes) === 0): ?>

                <div class="empty-state">

                    <span class="empty-icon" aria-hidden="true">⌕</span>

                    <h3>No recipes found</h3>

                    <p>Try changing your search or adjusting your filters.</p>

                    <a class="button-secondary" href="index.php">
                        View all recipes
                    </a>

                </div>

            <?php else: ?>

                <div class="recipe-grid">

                    <?php foreach ($recipes as $index => $recipe): ?>

                        <article class="recipe-card">

                            <div class="recipe-visual visual-<?= ($index % 4) + 1 ?>">

                                <span class="recipe-number">
                                    RECIPE <?= str_pad(
                                        (string) ($index + 1),
                                        2,
                                        '0',
                                        STR_PAD_LEFT
                                    ) ?>
                                </span>

                                <span class="visual-symbol" aria-hidden="true">
                                    <?= ['✳', '◒', '✿', '◉'][$index % 4] ?>
                                </span>

                                <span class="visual-caption">
                                    A little inspiration<br>for your table
                                </span>

                            </div>

                            <div class="recipe-content">

                                <div class="recipe-meta">

                                    <span class="time-pill">
                                        <?= (int) $recipe['cooking_time_minutes'] ?> min
                                        <span aria-hidden="true">·</span> est.
                                    </span>

                                    <span class="fresh-label">
                                        <?= e((string) $recipe['category']) ?>
                                    </span>

                                </div>

                                <h3><?= e((string) $recipe['title']) ?></h3>

                                <p class="recipe-description">
                                    <?= e((string) $recipe['description']) ?>
                                </p>

                                <a
                                    class="view-recipe-btn"
                                    href="recipe.php?id=<?= (int) $recipe['id'] ?>"
                                >
                                    View Recipe
                                </a>

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
<style>
    
/* RECIPE FILTERS */

.filter-form {
    width: 100%;
    max-width: 650px;
    margin-top: 25px;
}

.filter-options {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
    margin-top: 18px;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.filter-group label {
    font-size: 13px;
    font-weight: 600;
    color: #355b3c;
}

.filter-group select,
.filter-group input {
    width: 100%;
    min-width: 0;
    box-sizing: border-box;
    padding: 12px;
    border: 1px solid #deded7;
    border-radius: 8px;
    background: #fff;
    color: #333;
    font: inherit;
    font-size: 13px;
}

.filter-group select:focus,
.filter-group input:focus {
    outline: none;
    border-color: #527a50;
}

.filter-actions {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-top: 18px;
}

.apply-filters-btn {
    padding: 12px 22px;
    border: none;
    border-radius: 8px;
    background: #355b3c;
    color: white;
    font-weight: 600;
    cursor: pointer;
}

.apply-filters-btn:hover {
    background: #27452e;
}

.clear-filters {
    color: #527a50;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
}

.clear-filters:hover {
    text-decoration: underline;
}

@media (max-width: 650px) {
    .filter-options {
        grid-template-columns: 1fr;
    }

    .filter-actions {
        flex-wrap: wrap;
    }
}

</style>
</body>
</html>


