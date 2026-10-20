
CREATE TABLE IF NOT EXISTS recipes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  description TEXT NOT NULL,
  ingredients TEXT NOT NULL,
  instructions TEXT NOT NULL,
  cooking_time_minutes INT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO recipes (
  title,
  description,
  ingredients,
  instructions,
  cooking_time_minutes
) VALUES
(
  'Jollof Rice',
  'A flavorful West African rice dish.',
  'Rice, tomatoes, red bell pepper, onion, garlic, stock, and spices.',
  'Blend the vegetables. Cook the sauce with spices, add stock and rice, then simmer until tender.',
  50
),
(
  'Pancakes',
  'Soft and fluffy pancakes for breakfast.',
  'Flour, milk, egg, sugar, baking powder, and butter.',
  'Mix the ingredients, pour the batter into a buttered pan, flip, and cook until golden.',
  20
),
(
  'Chicken Fried Rice',
  'A savory rice dish with chicken and vegetables.',
  'Cooked rice, chicken, carrots, peas, eggs, and soy sauce.',
  'Cook the chicken, stir-fry the vegetables, add eggs and rice, then mix in the chicken and soy sauce.',
  30
),
(
  'Spaghetti with Tomato Sauce',
  'A simple pasta dish with tomato sauce.',
  'Spaghetti, tomatoes, onion, garlic, olive oil, and herbs.',
  'Boil the spaghetti. Cook the sauce ingredients, simmer, and combine with the drained pasta.',
  25
),
(
  'Banana Smoothie',
  'A creamy smoothie for breakfast or a snack.',
  'Bananas, milk, yogurt, and honey.',
  'Add all ingredients to a blender, blend until smooth, and serve.',
  5
);
