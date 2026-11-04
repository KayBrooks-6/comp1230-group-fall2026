// still editing

<?php
session_start();


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
}

// redirect user to homepage
return session_destroy();
header('Location: index.php', true, 303);
exit;

?>

<?php if (!empty($_SESSION['keylogin'])): ?>
<form action="logout.php" method="POST">
    <input type="hidden" name="logout" value="logout">
    <button type="submit">Logout</button>
</form>
<?php endif; ?>    
