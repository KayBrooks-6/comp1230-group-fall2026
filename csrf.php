<?php
// csfr.php for CSRF protection in our app with the three functions we need

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Generate or retrieve the session CSRF token
function csrfToken()
{
  if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['csrf_token'];
}

// Output the hidden input field for forms
function csrfField()
{
  $token = csrfToken();
  return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

// Validate the token on incoming POST requests
function requireCsrf()
{
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = $_POST['csrf_token'] ?? '';

    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $submittedToken)) {
      http_response_code(403);
      exit('403 Forbidden: Invalid or missing CSRF token.');
    }
  }
}
