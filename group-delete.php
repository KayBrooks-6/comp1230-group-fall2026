
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/csrf.php';
requireCsrf();

if (!isset($_SESSION['user'])) {
    header('Location: login.php', true, 303);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php', true, 303);
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header('Location: index.php', true, 303);
    exit;
}

try {
    $pdo = require __DIR__ . '/db.php';

    $statement = $pdo->prepare(
        'DELETE FROM study_groups WHERE id = :id'
    );

    $statement->execute([':id' => $id]);

    header('Location: index.php', true, 303);
    exit;
} catch (PDOException $e) {
    error_log('Database Error: ' . $e->getMessage());
    exit('Database error.');
}
