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
