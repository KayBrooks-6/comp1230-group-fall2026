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
        (study_groups.capacity - COUNT(memberships.user_id)) AS places_left
    FROM study_groups
    JOIN users ON study_groups.owner_id = users.id
    LEFT JOIN memberships ON study_groups.id = memberships.group_id
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

$currentUserId = isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Study Group Finder - Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
  <?php include __DIR__ . '/header.php'; ?>

  <main class="container py-4">
    <section class="card shadow-sm">
      <header class="card-header bg-white d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-0">Available Study Groups</h1>
        <?php if ($currentUserId !== null): ?>
          <a href="group-create.php" class="btn btn-primary btn-sm">Create Group</a>
        <?php endif; ?>
      </header>

      <section class="card-body p-0">
        <?php if (empty($groups)): ?>
          <p class="p-4 mb-0 text-secondary">No study groups available right now.</p>
        <?php else: ?>
          <section class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
              <thead class="table-dark">
                <tr>
                  <th scope="col">Course</th>
                  <th scope="col">Title</th>
                  <th scope="col">When</th>
                  <th scope="col">Where</th>
                  <th scope="col">Capacity</th>
                  <th scope="col">Places left</th>
                  <th scope="col">Created by</th>
                  <?php if ($currentUserId !== null): ?>
                    <th scope="col">Actions</th>
                  <?php endif; ?>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($groups as $group): ?>
                  <?php
                  $isOwner = ($currentUserId !== null && (int)$group['owner_id'] === $currentUserId);
                  $placesLeft = max(0, (int)$group['places_left']);
                  ?>
                  <tr>
                    <td class="fw-semibold"><?= htmlspecialchars($group['course_code'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                      <a href="group.php?id=<?= (int)$group['id'] ?>" class="text-decoration-none fw-semibold">
                        <?= htmlspecialchars($group['title'], ENT_QUOTES, 'UTF-8') ?>
                      </a>
                    </td>
                    <td><?= htmlspecialchars($group['meets_at'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($group['location'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= (int)$group['capacity'] ?></td>
                    <td><?= $placesLeft ?></td>
                    <td><?= htmlspecialchars($group['display_name'], ENT_QUOTES, 'UTF-8') ?></td>

                    <?php if ($currentUserId !== null): ?>
                      <td>
                        <?php if ($isOwner): ?>
                          <nav aria-label="Group actions" class="d-inline-flex gap-1">
                            <a href="group-edit.php?id=<?= (int)$group['id'] ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <form action="group-delete.php" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this group?');">
                              <?= csrfField() ?>
                              <input type="hidden" name="id" value="<?= (int)$group['id'] ?>">
                              <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                          </nav>
                        <?php endif; ?>
                      </td>
                    <?php endif; ?>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </section>
        <?php endif; ?>
      </section>
    </section>
  </main>

  <?php include __DIR__ . '/footer.php'; ?>
</body>

</html>