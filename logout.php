<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include __DIR__ . '/header.php'; ?>
    <?php include __DIR__ . '/footer.php'; ?>
</body>

</html>


<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/csrf.php';
requireCsrf();

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
