<?php
// header.php — shared navigation header
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once __DIR__ . '/csrf.php';
?>
<header>
  <nav>
    <a href="index.php">Home</a> |
    <a href="team.php">Team</a>

    <?php if (isset($_SESSION['user'])): ?>
      <a href="my-groups.php">My groups</a>
      | Logged in as <?= htmlspecialchars($_SESSION['user']['display_name'], ENT_QUOTES, 'UTF-8') ?>
      | <a href="group-create.php">Create Group</a>

      <form action="logout.php" method="POST" style="display:inline;">
        <?= csrfField() ?>
        <button type="submit">Log out</button>
      </form>

    <?php else: ?>
      | <a href="login.php">Log in</a> |
      <a href="register.php">Register</a>
    <?php endif; ?>

  </nav>
</header>