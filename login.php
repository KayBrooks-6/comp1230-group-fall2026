<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errors = [];
$username = '';


// change dbname and name to your id
try {
    $pdo = require __DIR__ . '/db.php';
} catch (PDOException $e) {
    $errors[] = "Database error.";
    error_log("Database Error: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $password = $_POST['password'] ?? '';
    $username = trim((string)($_POST['username'] ?? ''));

    if (empty($username) || empty($password)) {
        $errors[] = "Both username and password are required.";
    }

    if (empty($username) || empty($password)) {
        $errors[] = "Username or password is incorrect.";
    } else {
        try {

            $statement = $pdo->prepare("SELECT id, username, display_name, password_hash FROM users WHERE username = :username LIMIT 1");
            $statement->execute([':username' => $username]);
            $user = $statement->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['logged_in'] = true;
                $_SESSION['last_seen'] = time();
                $_SESSION['user'] = [
                    'id'           => $user['id'],
                    'username'     => $user['username'],
                    'display_name' => $user['display_name']
                ];

                header('Location: index.php', true, 303);
                exit;
            } else {
                $errors[] = "Username or password is incorrect.";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error.";
            error_log("Database error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include __DIR__ . '/header.php'; ?>

    <main>
        <h2>Login Page</h2>
        <div class="test-accounts" style="border: 1px solid #ccc; padding: 10px; margin-bottom: 15px; background: #f9f9f9;">
            <p><strong>Test Accounts:</strong></p>
            <p>Username: <code>tester1</code> | Password: <code>Study-1230</code></p>
            <p>Username: <code>tester2</code> | Password: <code>Study-1230</code></p>
        </div>

        <?php if (!empty($errors)): ?>
            <div>
                <?php foreach ($errors as $error): ?>
                    <?php if (is_string($error)): ?>
                        <p style="color: red;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password">
            </div>
            <button type="submit">Log In</button>
        </form>

        <div style="margin-top: 15px;">
            <p>Don't have an account? <a href="register.php"><button type="button">Register here</button></a></p>
        </div>

    </main>
</body>

</html>