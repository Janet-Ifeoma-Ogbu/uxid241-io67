<?php
declare(strict_types=1);

require_once __DIR__ . '/database.php';

function e(string $value): string
{
  return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$search_term = '';
$error_message = '';
$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $search_term = trim($_POST['search_term'] ?? '');

  if ($search_term === '') {
    $error_message = 'Please enter a recipe name.';
  } elseif (strlen($search_term) < 2) {
    $error_message = 'Enter at least 2 characters.';
  } else {
    $stmt = $mysqli->prepare(
      'SELECT id, title, description, ingredients,
              instructions, cooking_time_minutes, created_at
       FROM cookbook
       WHERE title LIKE ?
       ORDER BY title ASC'
    );

    $search_pattern = '%' . $search_term . '%';
    $stmt->bind_param('s', $search_pattern);
    $stmt->execute();

    $result = $stmt->get_result();
  }
} else {
  $sql = 'SELECT id, title, description, ingredients,
        instructions, cooking_time_minutes, created_at
        FROM recipes
        ORDER BY title ASC';

$result = mysqli_query($mysqli, $sql);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cookbookery Recipe Search</title>
</head>
<body>
  <h1>Cookbookery</h1>
  <p>Find something delicious to make!</p>

  <form method="post" action="index.php">
    <label for="search_term">Search for a recipe:</label>
    <input
      type="text"
      id="search_term"
      name="search_term"
      value="<?= e($search_term) ?>"
      placeholder="e.g. Jollof Rice"
    >
    <button type="submit">Search</button>
  </form>

  <?php if ($error_message !== ''): ?>
    <p><?= e($error_message) ?></p>
  <?php endif; ?>

  <?php if ($result !== null): ?>
    <?php if (mysqli_num_rows($result) === 0): ?>
      <h2>No recipes found</h2>
      <p>Try another recipe name.</p>
    <?php else: ?>
      <h2>
        <?= $search_term !== '' ? 'Search Results' : 'All Recipes' ?>
      </h2>

      <?php while ($recipe = mysqli_fetch_assoc($result)): ?>
        <article>
          <h3><?= e($recipe['title']) ?></h3>
          <p><?= e($recipe['description']) ?></p>

          <h4>Ingredients</h4>
          <p><?= e($recipe['ingredients']) ?></p>

          <h4>Instructions</h4>
          <p><?= e($recipe['instructions']) ?></p>

          <p>
            <strong>Cooking time:</strong>
            <?= e((string) $recipe['cooking_time_minutes']) ?> minutes
          </p>

          <hr>
        </article>
      <?php endwhile; ?>

    <?php endif; ?>

    <?php mysqli_free_result($result); ?>
  <?php endif; ?>

  <?php
  if (isset($stmt)) {
    $stmt->close();
  }

  $mysqli->close();
  ?>
</body>
</html>


