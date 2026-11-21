<?php
// header.php — shared navigation header
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once __DIR__ . '/csrf.php';
?>
<header>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm mb-4" aria-label="Primary navigation">
    <section class="container">
      <a class="navbar-brand fw-bold" href="index.php">Study Group Finder</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <nav class="collapse navbar-collapse" id="mainNav" aria-label="Navigation menu">
        <ul class="navbar-nav me-auto">
          <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="team.php">Team</a></li>
          <?php if (isset($_SESSION['user'])): ?>
            <li class="nav-item"><a class="nav-link" href="my-groups.php">My groups</a></li>
            <li class="nav-item"><a class="nav-link" href="group-create.php">Create Group</a></li>
          <?php endif; ?>
        </ul>

        <ul class="navbar-nav ms-auto align-items-lg-center gap-2">
          <?php if (isset($_SESSION['user'])): ?>
            <li class="nav-item">
              <p class="navbar-text mb-0 text-light">
                Logged in as <strong class="text-info"><?= htmlspecialchars($_SESSION['user']['display_name'], ENT_QUOTES, 'UTF-8') ?></strong>
              </p>
            </li>
            <li class="nav-item">
              <form action="logout.php" method="POST" class="d-inline">
                <?= csrfField() ?>
                <button type="submit" class="btn btn-outline-light btn-sm">Log out</button>
              </form>
            </li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="login.php">Log in</a></li>
            <li class="nav-item"><a class="btn btn-primary btn-sm" href="register.php">Register</a></li>
          <?php endif; ?>
        </ul>
      </nav>
    </section>
  </nav>
</header>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>