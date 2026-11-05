<?php
declare(strict_types=1);

$config = require __DIR__ . '/config.local.php';

$mysqli = mysqli_connect(
    $config['host'],
    $config['username'],
    $config['password'],
    $config['database']
);

if (!$mysqli) {
    error_log('Database connection failed: ' . mysqli_connect_error());
    exit('Unable to connect to the database. Please try again later.');
}

mysqli_set_charset($mysqli, 'utf8mb4');
