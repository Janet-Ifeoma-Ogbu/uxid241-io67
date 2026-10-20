# uxid241-io67

# Cookbookery

## Project Description

Cookbookery is a PHP and MySQL cookbook application that displays recipes stored in a relational database. This Alpha milestone establishes the database schema, sample recipe data, and PHP read page.

## Technologies

- PHP
- MySQL
- mysqli
- HTML

## Database Setup

1. Start Apache and MySQL in your local development environment.
2. Create a MySQL database named `idm232`.
3. Import `schema.sql` through phpMyAdmin or the MySQL command line.
4. Configure your local database connection in `database.php`.
5. Open `index.php` through your local PHP server.

## Current Features

- Displays recipes stored in the database.
- Shows recipe titles, descriptions, ingredients, instructions, and cooking times.
- Escapes dynamic HTML output using `htmlspecialchars`.
- Uses mysqli for database access and enables strict database error reporting.

## AI Use

AI assistance was used to help understand PHP and mysqli, troubleshoot database connection and table-name issues
