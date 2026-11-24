<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/csrf.php';
requireCsrf();

$errors = [];
$username = '';

try {
    $pdo = require __DIR__ . '/db.php';
} catch (PDOException $e) {
    $errors[] = 'Database error.';
    error_log('Database Error: ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $username = trim((string)($_POST['username'] ?? ''));

    if ($username === '' || $password === '') {
        $errors[] = 'Username or password is incorrect.';
    } else {
        try {
            $statement = $pdo->prepare(
                'SELECT id, username, display_name, password_hash FROM users WHERE username = :username LIMIT 1'
            );
            $statement->execute([':username' => $username]);
            $user = $statement->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['logged_in'] = true;
                $_SESSION['last_seen'] = time();
                $_SESSION['user'] = [
                    'id'           => $user['id'],
                    'username'     => $user['username'],
                    'display_name' => $user['display_name'],
                ];

                header('Location: index.php', true, 303);
                exit;
            } else {
                $errors[] = 'Username or password is incorrect.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Database error.';
            error_log('Database error: ' . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <?php include __DIR__ . '/header.php'; ?>

    <main class="container py-4" style="max-width: 480px;">
        <section class="card shadow-sm">
            <header class="card-header bg-white">
                <h1 class="h3 mb-0">Login</h1>
            </header>

            <section class="card-body">
                <aside class="alert alert-secondary small" aria-label="Test accounts">
                    <p class="fw-bold mb-1">Test Accounts:</p>
                    <p class="mb-1">Username: <code>tester1</code> | Password: <code>Study-1230</code></p>
                    <p class="mb-0">Username: <code>tester2</code> | Password: <code>Study-1230</code></p>
                </aside>

                <?php if (!empty($errors)): ?>
                    <section class="error alert alert-danger py-2" role="alert">
                        <?php foreach ($errors as $error): ?>
                            <?php if (is_string($error)): ?>
                                <p class="mb-0"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </section>
                <?php endif; ?>

                <form action="login.php" method="POST">
                    <?= csrfField() ?>

                    <p class="mb-3">
                        <label for="username" class="form-label">Username:</label>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            class="form-control"
                            value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>"
                            required
                        >
                    </p>

                    <p class="mb-3">
                        <label for="password" class="form-label">Password:</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            required
                        >
                    </p>

                    <button type="submit" class="btn btn-primary w-100">Log In</button>
                </form>

                <p class="mt-3 mb-0 text-center text-secondary">
                    Don't have an account?
                    <a href="register.php" class="ms-1">Register here</a>
                </p>
            </section>
        </section>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>
</body>

</html>