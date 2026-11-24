<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!empty($_SESSION['logged_in'])) {
  header('Location: index.php', true, 303);
  exit;
}

require_once __DIR__ . '/csrf.php';
requireCsrf();

$errors = [];
$username = '';
$display_name = '';
$pdo = null;

try {
  $pdo = require __DIR__ . '/db.php';
} catch (PDOException $e) {
  $errors[] = 'Database error.';
  error_log('Database Error: ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $display_name = trim($_POST['display_name'] ?? '');
  $username = mb_strtolower(trim($_POST['username'] ?? ''), 'UTF-8');
  $password = $_POST['password'] ?? '';
  $password_confirm = $_POST['password_confirm'] ?? '';

  // Validate the username
  if ($username === '') {
    $errors['username'] = 'Username is needed.';
  } elseif (mb_strlen($username, 'UTF-8') < 3 || mb_strlen($username, 'UTF-8') > 20) {
    $errors['username'] = 'Username must be between 3 and 20 characters.';
  } elseif (!preg_match('/^[a-z0-9_]+$/', $username)) {
    $errors['username'] = 'Username can only contain lowercase letters, digits and underscores';
  }

  // Validate the display name
  if ($display_name === '') {
    $errors['display_name'] = 'Display name is required.';
  } elseif (mb_strlen($display_name, 'UTF-8') > 50) {
    $errors['display_name'] = 'Input is too long. Maximum input of 50 characters.';
  }

  // Validate the password
  if (mb_strlen($password, 'UTF-8') < 8) {
    $errors['password'] = 'Password must be at least 8 characters.';
  } elseif ($password !== $password_confirm) {
    $errors['password_confirm'] = 'Passwords do not match!';
  }

  if (empty($errors) && $pdo instanceof PDO) {
    try {
      $statement = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ? LIMIT 1');
      $statement->execute([$username]);

      if ((int)$statement->fetchColumn() > 0) {
        $errors['username'] = 'That username is already taken.';
      } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare('INSERT INTO users (username, password_hash, display_name) VALUES (?, ?, ?)');
        $stmt->execute([$username, $password_hash, $display_name]);

        $newUserId = (int)$pdo->lastInsertId();

        // Automatically log the user in after registration
        session_regenerate_id(true);
        $_SESSION['logged_in'] = true;
        $_SESSION['user'] = [
          'id'           => $newUserId,
          'username'     => $username,
          'display_name' => $display_name,
        ];

        header('Location: index.php', true, 303);
        exit;
      }
    } catch (PDOException $e) {
      if ($e->getCode() === '23000') {
        $errors['username'] = 'That username is already taken.';
      } else {
        $errors[] = 'Database error.';
        error_log('Database Error: ' . $e->getMessage());
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
  <?php include __DIR__ . '/header.php'; ?>

  <main class="container py-4" style="max-width: 520px;">
    <article class="card shadow-sm">
      <header class="card-header bg-white">
        <h1 class="h3 mb-0">Register</h1>
      </header>

      <section class="card-body">
        <?php if (!empty($errors)): ?>
          <aside class="error alert alert-danger py-2" role="alert">
            <ul class="mb-0 ps-3">
              <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
              <?php endforeach; ?>
            </ul>
          </aside>
        <?php endif; ?>

        <form action="register.php" method="POST">
          <?= csrfField() ?>

          <section class="mb-3">
            <label for="username" class="form-label">Username:</label>
            <input type="text" id="username" name="username" class="form-control"
              value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>"
              minlength="3" maxlength="20" pattern="[a-z0-9_]+" required>
          </section>

          <section class="mb-3">
            <label for="display_name" class="form-label">Display Name:</label>
            <input type="text" id="display_name" name="display_name" class="form-control"
              value="<?= htmlspecialchars($display_name, ENT_QUOTES, 'UTF-8') ?>"
              maxlength="50" required>
          </section>

          <section class="mb-3">
            <label for="password" class="form-label">Password:</label>
            <input type="password" id="password" name="password" class="form-control"
              minlength="8" required>
          </section>

          <section class="mb-3">
            <label for="password_confirm" class="form-label">Confirm Password:</label>
            <input type="password" id="password_confirm" name="password_confirm" class="form-control"
              minlength="8" required>
          </section>

          <button type="submit" class="btn btn-primary w-100">Submit</button>
        </form>
      </section>
    </article>
  </main>

  <?php include __DIR__ . '/footer.php'; ?>
</body>

</html>