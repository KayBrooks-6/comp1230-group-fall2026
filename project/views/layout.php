<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($title ?? 'Study Group Finder') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= e(url('/style.css')) ?>">
</head>

<body class="bg-light">
  <header class="border-bottom bg-white">
    <nav class="navbar navbar-expand-lg container" aria-label="Primary navigation">
      <a class="navbar-brand fw-semibold" href="<?= e(url('/')) ?>">Study Group Finder</a>
      <ul class="navbar-nav me-auto gap-2">
        <li class="nav-item"><a class="nav-link" href="<?= e(url('/')) ?>">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= e(url('/team')) ?>">Team</a></li>
        <?php if (!empty($_SESSION['user'])): ?>
          <li class="nav-item"><a class="nav-link" href="<?= e(url('/my-groups')) ?>">My Groups</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= e(url('/groups/add')) ?>">New Group</a></li>
        <?php endif; ?>
      </ul>

      <section aria-label="Account actions" class="d-flex align-items-center gap-2">
        <?php if (!empty($_SESSION['user'])): ?>
          <span class="text-secondary small">Logged in as <?= e($_SESSION['user']['display_name'] ?? '') ?></span>
          <form action="<?= e(url('/logout')) ?>" method="POST" class="d-inline">
            <?= csrfField() ?>
            <button type="submit" class="btn btn-sm btn-outline-secondary">Log out</button>
          </form>
        <?php else: ?>
          <a href="<?= e(url('/login')) ?>" class="btn btn-sm btn-outline-primary">Log in</a>
          <a href="<?= e(url('/register')) ?>" class="btn btn-sm btn-primary">Register</a>
        <?php endif; ?>
      </section>
    </nav>
  </header>

  <main class="container py-4">
    <?= $content ?>
  </main>

  <footer class="border-top bg-white py-3 mt-5">
    <p class="container text-secondary small mb-0">COMP1230 Group Project · Team 9</p>
  </footer>
</body>

</html>