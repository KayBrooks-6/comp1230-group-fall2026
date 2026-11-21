<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/csrf.php';
$pdo = require __DIR__ . '/db.php';

$stmt = $pdo->query("
    SELECT
        study_groups.*,
        users.display_name,
        COUNT(memberships.user_id) AS member_count,
        study_groups.capacity - COUNT(memberships.user_id) AS places_left
    FROM study_groups
    JOIN users
        ON study_groups.owner_id = users.id
    LEFT JOIN memberships
        ON study_groups.id = memberships.group_id
    GROUP BY
        study_groups.id,
        study_groups.owner_id,
        study_groups.course_code,
        study_groups.title,
        study_groups.description,
        study_groups.meets_at,
        study_groups.location,
        study_groups.capacity,
        users.display_name
    ORDER BY study_groups.meets_at ASC
");

$groups = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Study Group Finder - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/header.php'; ?>
    <main>
        <h1>Available Study Groups</h1>
        <?php if (empty($groups)): ?>
            <p>No study groups available right now.</p>
        <?php else: ?>
            <table border="1" cellpadding="5" cellspacing="0">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Title</th>
                        <th>When</th>
                        <th>Where</th>
                        <th>Capacity</th>
                        <th>Places left</th>
                        <th>Created by</th>
                        <?php if (isset($_SESSION['user']['id'])): ?>
                            <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($groups as $group): ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars($group['course_code'], ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <a href="group.php?id=<?= (int)$group['id'] ?>">
                                    <?= htmlspecialchars($group['title'], ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            </td>
                            <td>
                                <?= htmlspecialchars($group['meets_at'], ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($group['location'], ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <?= (int)$group['capacity'] ?>
                            </td>
                            <td>
                                <?= (int)$group['places_left'] ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($group['display_name'], ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <?php if (isset($_SESSION['user']['id'])): ?>
                                <td>
                                    <?php if (
                                        (int)$group['owner_id'] ===
                                        (int)$_SESSION['user']['id']
                                    ): ?>
                                        <a href="group-edit.php?id=<?= (int)$group['id'] ?>">
                                            Edit
                                        </a>
                                        <form
                                            action="group-delete.php"
                                            method="POST"
                                            style="display:inline;"
                                            onsubmit="return confirm('Are you sure you want to delete this group?');">
                                            <?= csrfField() ?>
                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= (int)$group['id'] ?>">
                                            <button type="submit">Delete</button>
                                        </form>
                                    <?php else: ?>
                                        <span>—</span>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
    <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>