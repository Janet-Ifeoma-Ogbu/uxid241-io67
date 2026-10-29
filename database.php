<?php
declare(strict_types=1);

mysqli_report(
    MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT
);

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'idm232';
$mysqli = new mysqli(
    $host,
    $username,
    $password,
    $database
);

$mysqli->set_charset('utf8mb4');
