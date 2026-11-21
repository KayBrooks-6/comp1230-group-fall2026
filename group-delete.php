<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/csrf.php';
requireCsrf();

if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php', true, 303);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php', true, 303);
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(404);
    exit('Study group not found.');
}

try {
    $pdo = require __DIR__ . '/db.php';

    $checkStmt = $pdo->prepare(
        'SELECT owner_id FROM study_groups WHERE id = :id'
    );

    $checkStmt->execute([':id' => $id]);
    $group = $checkStmt->fetch();

    if (!$group) {
        http_response_code(404);
        exit('Study group not found.');
    }

    if ((int)$group['owner_id'] !== (int)$_SESSION['user']['id']) {
        http_response_code(403);
        exit('You can only change your own study groups.');
    }

    $statement = $pdo->prepare(
        'DELETE FROM study_groups
         WHERE id = :id
           AND owner_id = :owner_id'
    );

    $statement->execute([
        ':id' => $id,
        ':owner_id' => (int)$_SESSION['user']['id']
    ]);

    header('Location: index.php', true, 303);
    exit;

} catch (PDOException $e) {
    error_log('Database Error: ' . $e->getMessage());
    http_response_code(500);
    exit('Database error.');
}