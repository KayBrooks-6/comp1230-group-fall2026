<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['user'])) {
  header('Location: login.php', true, 303);
  exit;
}

$pdo = require __DIR__ . '/db.php';
$userId = $_SESSION['user']['id'];

try {
  // groups owned by the user
  $ownedStmt = $pdo->prepare("
        SELECT study_groups.*, users.display_name 
        FROM study_groups 
        JOIN users ON study_groups.owner_id = users.id 
        WHERE study_groups.owner_id = ? 
        ORDER BY study_groups.meets_at ASC
    ");
  $ownedStmt->execute([$userId]);
  $ownedGroups = $ownedStmt->fetchAll();

  // groups joined by the user 
  $joinedStmt = $pdo->prepare("
        SELECT study_groups.*, users.display_name 
        FROM memberships 
        JOIN study_groups ON memberships.group_id = study_groups.id 
        JOIN users ON study_groups.owner_id = users.id 
        WHERE memberships.user_id = ? 
        ORDER BY study_groups.meets_at ASC
    ");
  $joinedStmt->execute([$userId]);
  $joinedGroups = $joinedStmt->fetchAll();
} catch (PDOException $e) {
  error_log('Database Error: ' . $e->getMessage());
  exit('Database error.');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Study Groups</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>

<body class="bg-light">
  <?php include __DIR__ . '/header.php'; ?>

  <main class="container py-4">
    <h1 class="h3 mb-4">My Study Groups Dashboard</h1>

    <section class="mb-4" aria-labelledby="owned-groups-heading">
      <article class="card shadow-sm">
        <header class="card-header bg-white">
          <h2 id="owned-groups-heading" class="h5 mb-0">Groups I Own</h2>
        </header>
        <section class="card-body">
          <?php if (empty($ownedGroups)): ?>
            <p class="mb-0 text-secondary">You don't own any study groups yet.</p>
          <?php else: ?>
            <ul class="list-group list-group-flush">
              <?php foreach ($ownedGroups as $g): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                  <a href="group.php?id=<?= (int)$g['id'] ?>" class="text-decoration-none fw-semibold">
                    <?= htmlspecialchars($g['title'], ENT_QUOTES, 'UTF-8') ?>
                  </a>
                  <strong class="text-secondary"><?= htmlspecialchars($g['course_code'], ENT_QUOTES, 'UTF-8') ?></strong>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </section>
      </article>
    </section>

    <section aria-labelledby="joined-groups-heading">
      <article class="card shadow-sm">
        <header class="card-header bg-white">
          <h2 id="joined-groups-heading" class="h5 mb-0">Groups I Joined</h2>
        </header>
        <section class="card-body">
          <?php if (empty($joinedGroups)): ?>
            <p class="mb-0 text-secondary">You haven't joined any study groups yet.</p>
          <?php else: ?>
            <ul class="list-group list-group-flush">
              <?php foreach ($joinedGroups as $g): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                  <a href="group.php?id=<?= (int)$g['id'] ?>" class="text-decoration-none fw-semibold">
                    <?= htmlspecialchars($g['title'], ENT_QUOTES, 'UTF-8') ?>
                  </a>
                  <strong class="text-secondary"><?= htmlspecialchars($g['course_code'], ENT_QUOTES, 'UTF-8') ?></strong>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </section>
      </article>
    </section>
  </main>

  <?php include __DIR__ . '/footer.php'; ?>
</body>

</html>