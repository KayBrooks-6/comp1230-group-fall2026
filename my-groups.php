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
  <title>My Study Groups</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php include __DIR__ . '/header.php'; ?>

  <main class="container mt-4">
    <h1>My Study Groups Dashboard</h1>

    <section class="mb-5">
      <h2>Groups I Own</h2>
      <?php if (empty($ownedGroups)): ?>
        <p>You don't own any study groups yet.</p>
      <?php else: ?>
        <!-- Render table or list for owned groups -->
        <ul>
          <?php foreach ($ownedGroups as $g): ?>
            <li><a href="group.php?id=<?= $g['id'] ?>"><?= htmlspecialchars($g['title']) ?></a> (<?= htmlspecialchars($g['course_code']) ?>)</li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </section>

    <section>
      <h2>Groups I Joined</h2>
      <?php if (empty($joinedGroups)): ?>
        <p>You haven't joined any study groups yet.</p>
      <?php else: ?>
        <ul>
          <?php foreach ($joinedGroups as $g): ?>
            <li><a href="group.php?id=<?= $g['id'] ?>"><?= htmlspecialchars($g['title']) ?></a> (<?= htmlspecialchars($g['course_code']) ?>)</li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </section>
  </main>
  <?php include __DIR__ . '/footer.php'; ?>
</body>

</html>