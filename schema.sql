```sql
CREATE DATABASE IF NOT EXISTS cookbook;

USE cookbook;

CREATE TABLE IF NOT EXISTS recipes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  recipe_name VARCHAR(150) NOT NULL,
  description TEXT NOT NULL,
  prep_time_minutes INT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO recipes (
  recipe_name,
  description,
  prep_time_minutes
) VALUES
  (
    'Jollof Rice',
    'A flavorful West African rice dish cooked in a tomato and pepper sauce.',
    45
  ),
  (
    'Pancakes',
    'Fluffy pancakes made with flour, milk, eggs, and butter.',
    20
  ),
  (
    'Chicken Stir Fry',
    'Chicken and fresh vegetables cooked with a savory sauce.',
    30
  ),
  (
    'Pasta Alfredo',
    'Pasta served with a creamy garlic and Parmesan sauce.',
    25
  ),
  (
    'Vegetable Soup',
    'A hearty soup made with assorted vegetables and seasonings.',
    35
  );
```