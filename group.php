<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/csrf.php';
$pdo = require __DIR__ . '/db.php';

$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);

$group_id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

if (!$group_id || $group_id < 1) {
    http_response_code(404);
    exit('<p class="error">Invalid ID.</p>');
}

$stmt = $pdo->prepare("
    SELECT
        study_groups.*,
        users.display_name AS owner_name,
        COUNT(memberships.user_id) AS number_of_member,
        (study_groups.capacity - COUNT(memberships.user_id)) AS places_left
    FROM study_groups
    LEFT JOIN users ON study_groups.owner_id = users.id
    LEFT JOIN memberships ON study_groups.id = memberships.group_id
    WHERE study_groups.id = ?
    GROUP BY study_groups.id, users.display_name
");
$stmt->execute([$group_id]);
$group = $stmt->fetch();

if (!$group) {
    http_response_code(404);
    exit('<p class="error">Group not found.</p>');
}

$memberStmt = $pdo->prepare("
    SELECT users.display_name
    FROM memberships
    JOIN users ON memberships.user_id = users.id
    WHERE memberships.group_id = ?
    ORDER BY users.display_name ASC
");
$memberStmt->execute([$group_id]);
$members = $memberStmt->fetchAll();

$logged_in = isset($_SESSION['user']['id']) || (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true);
$user_id = isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : null;

$is_owner = false;
$is_member = false;
$places_Left = max(0, (int)$group['places_left']);

if ($logged_in && $user_id !== null) {
    $is_owner = ((int)$group['owner_id'] === $user_id);

    $checkMemberStmt = $pdo->prepare("SELECT COUNT(*) FROM memberships WHERE group_id = ? AND user_id = ?");
    $checkMemberStmt->execute([$group_id, $user_id]);
    $is_member = (int)$checkMemberStmt->fetchColumn() > 0;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($group['title'], ENT_QUOTES, 'UTF-8') ?> - Study Group</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <?php include __DIR__ . '/header.php'; ?>

    <main class="container py-4">
        <article class="card shadow-sm mb-4">
            <header class="card-header bg-white">
                <h1 class="h3 mb-1"><?= htmlspecialchars($group['title'], ENT_QUOTES, 'UTF-8') ?></h1>
                <p class="mb-0 text-secondary"><strong>Course:</strong> <?= htmlspecialchars($group['course_code'], ENT_QUOTES, 'UTF-8') ?></p>
            </header>

            <section class="card-body">
                <p class="mb-3"><?= htmlspecialchars($group['description'], ENT_QUOTES, 'UTF-8') ?></p>

                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item px-0"><strong>Owner:</strong> <?= htmlspecialchars($group['owner_name'], ENT_QUOTES, 'UTF-8') ?></li>
                    <li class="list-group-item px-0"><strong>Time:</strong> <?= htmlspecialchars($group['meets_at'], ENT_QUOTES, 'UTF-8') ?></li>
                    <li class="list-group-item px-0"><strong>Place:</strong> <?= htmlspecialchars($group['location'], ENT_QUOTES, 'UTF-8') ?></li>
                    <li class="list-group-item px-0"><strong>Capacity:</strong> <?= htmlspecialchars((string)$group['capacity'], ENT_QUOTES, 'UTF-8') ?></li>
                    <li class="list-group-item px-0"><strong>Places left:</strong> <?= htmlspecialchars((string)$places_Left, ENT_QUOTES, 'UTF-8') ?></li>
                </ul>

                <section aria-labelledby="members-heading" class="mb-4">
                    <h2 id="members-heading" class="h5">Members List</h2>
                    <?php if (empty($members)): ?>
                        <p class="text-secondary mb-0">No members</p>
                    <?php else: ?>
                        <ul class="list-group">
                            <?php foreach ($members as $member): ?>
                                <li class="list-group-item"><?= htmlspecialchars($member['display_name'], ENT_QUOTES, 'UTF-8') ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </section>

                <?php if (!empty($success)): ?>
                    <p class="alert alert-success" role="status"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <p class="error alert alert-danger" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>

                <section aria-label="Membership actions">
                    <?php if ($logged_in): ?>
                        <?php if ($is_owner): ?>
                            <p class="mb-0 text-secondary"><strong>Owner of the group</strong></p>
                        <?php elseif ($is_member): ?>
                            <form action="group-leave.php" method="POST" class="d-inline">
                                <?= csrfField() ?>
                                <input type="hidden" name="id" value="<?= (int)$group['id'] ?>">
                                <button type="submit" class="btn btn-warning">Leave</button>
                            </form>
                        <?php elseif ($places_Left > 0): ?>
                            <form action="group-join.php" method="POST" class="d-inline">
                                <?= csrfField() ?>
                                <input type="hidden" name="id" value="<?= (int)$group['id'] ?>">
                                <button type="submit" class="btn btn-success">Join</button>
                            </form>
                        <?php else: ?>
                            <p class="error alert alert-danger mb-0" role="alert">This study group is full.</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="mb-0"><a href="login.php" class="btn btn-primary btn-sm">Log in</a></p>
                    <?php endif; ?>
                </section>
            </section>
        </article>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>
</body>

</html>