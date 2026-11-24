<?php
/*
 * db.php — the connection to the lab database. It RETURNS the connection:
 *     $pdo = require __DIR__ . '/db.php';
 * Change the four values to your own, account prefix included.
 */
$host     = 'localhost';
$database = 'f6641010_project';
$user     = 'f6641010_tester1';
$password = 'tester1';

return new PDO(
    "mysql:host=$host;dbname=$database;charset=utf8mb4",
    $user,
    $password,
    [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]
);
