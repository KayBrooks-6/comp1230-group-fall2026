<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="index.php">Home</a>
            <a class="nav-link" href="team.php">Team</a>
            <?php if (isset($_SESSION['user'])): ?>
                <span class="navbar-text">
                    Logged in as <?= htmlspecialchars($_SESSION['user']['display_name']) ?>
                </span>
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
</body>

</html>


<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function signOut(): bool
{
    // strict post only 
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        header('Allow: POST');
        exit;
    }

    $_SESSION = array();

    $cookieParams = session_get_cookie_params();

    setcookie(session_name(), '', [
        'expires' => time() - 3600,
        'path'  => $cookieParams['path'],
        'domain' => $cookieParams['domain'],
        'secure' => $cookieParams['secure'],
        'httponly' => $cookieParams['httponly']
    ]);
    return session_destroy();
}

signOut();

// redirect user to homepage 
header('Location: index.php', true, 303);
exit;
