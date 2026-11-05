<?php
// header.php — shared navigation header
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
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