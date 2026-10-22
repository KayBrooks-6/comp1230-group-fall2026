<?php
session_start();
$pdo = require __DIR__ . '/db.php';

// get all study groups with the owner's display name, ordered by earliest meeting time first
$stmt = $pdo->query("
    SELECT study_groups.*, users.display_name 
    FROM study_groups 
    JOIN users ON study_groups.owner_id = users.id 
    ORDER BY study_groups.meets_at ASC
");
$groups = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Study Group Finder - Home</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header>
    <nav>
      <a href="index.php">Home</a> |
      <a href="team.php">Team</a>
      <?php if (isset($_SESSION['user'])): ?>
        | Logged in as <?= htmlspecialchars($_SESSION['user']['display_name']) ?>
        | <a href="group-create.php">Create Group</a>
        <form action="logout.php" method="POST" style="display:inline;">
          <button type="submit">Log out</button>
        </form>
      <?php else: ?>
        | <a href="login.php">Log in</a> |
        <a href="register.php">Register</a>
      <?php endif; ?>
    </nav>
  </header>

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
            <th>Created by</th>
            <?php if (isset($_SESSION['user'])): ?>
              <th>Actions</th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($groups as $group): ?>
            <tr>
              <td><?= htmlspecialchars($group['course_code']) ?></td>
              <td><?= htmlspecialchars($group['title']) ?></td>
              <td><?= htmlspecialchars($group['meets_at']) ?></td>
              <td><?= htmlspecialchars($group['location']) ?></td>
              <td><?= htmlspecialchars($group['capacity']) ?></td>
              <td><?= htmlspecialchars($group['display_name']) ?></td>

              <?php if (isset($_SESSION['user'])): ?>
                <td>
                  <!-- the edit link -->
                  <a href="group-edit.php?id=<?= $group['id'] ?>">Edit</a>

                  <!-- delete form -->
                  <form action="group-delete.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this group?');">
                    <input type="hidden" name="id" value="<?= $group['id'] ?>">
                    <button type="submit">Delete</button>
                  </form>
                </td>
              <?php endif; ?>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </main>
</body>

</html>