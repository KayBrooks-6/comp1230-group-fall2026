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

    // check ownership on the server before deleting
    $checkStmt = $pdo->prepare('SELECT owner_id FROM study_groups WHERE id = :id');
    $checkStmt->execute([':id' => $id]);
    $group = $checkStmt->fetch();

    // if the group doesn't exist or user is not the owner, return 403
    if (!$group || (int)$group['owner_id'] !== (int)$_SESSION['user']['id']) {
        http_response_code(403);
        exit('<div class="error">You can only change your own study groups.</div>');
    }

    // if the owner, then they can delete the group
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
