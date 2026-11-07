 <!-- still editing -->

 <?php
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }

  $errors = [];
  $username = '';
  $display_name = '';

  try {
    $pdo = require __DIR__ . '/db.php';
  } catch (PDOException $e) {
    $errors[] = "Database error.";
    error_log("Database Error: " . $e->getMessage());
  }

  // server //
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $display_name = trim($_POST['display_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $username = mb_strtolower($username, 'UTF-8');
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

    // validate the display name
    if ($display_name === '') {
      $errors['display_name'] = "Display name is required.";
    } elseif (mb_strlen($display_name, 'UTF-8') > 50) {
      $errors['display_name'] = "Input is too long. Maximum input of 50 characters.";
    }

    // Validate the password
    if (mb_strlen($password, 'UTF-8') < 8) {
      $errors['password'] = "Password must be at least 8 characters.";
    }
    if ($password !== $password_confirm) {
      $errors['password'] = "Passwords do not match!";
    }

    if (empty($errors)) {
      try {

        $statement = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? LIMIT 1");
        $statement->execute([$username]);

        if ((int)$statement->fetchColumn() > 0) {
          $errors['username'] = 'Username is already taken.';
        } else {

          $password_hash = password_hash($password, PASSWORD_DEFAULT);

          $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, display_name) VALUES (?, ?, ?)");
          $stmt->execute([$username, $password_hash, $display_name]);

          $newUserId = $pdo->lastInsertId();

          // this is to automatically log the user in after registration
          session_regenerate_id(true);
          $_SESSION['logged_in'] = true;
          $_SESSION['user'] = [
            'id'           => $newUserId,
            'username'     => $username,
            'display_name' => $display_name
          ];

          header('Location: index.php', true, 303);
          exit;
        }
      } catch (PDOException $e) {
        $errors[] = "Database error.";
        error_log("Database Error: " . $e->getMessage());
        exit;
      }
    }
  }

  ?>

 <!DOCTYPE html>
 <html lang="en">

 <head>
   <meta charset="UTF-8">
   <title>Registration</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
 </head>

 <body>
   <?php include __DIR__ . '/header.php'; ?>

   <main>
     <h1>Register</h1>

     <!-- Register Form error-->
     <?php if (!empty($errors)): ?>
       <span style="color: red; font-size: 10px;">
         <?php foreach ($errors as $err): ?>
           <?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?><br>
         <?php endforeach; ?>
       </span>
     <?php endif; ?>

     <!-- registration form -->
     <table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse;">
       <tr>
         <td>
           <form action="register.php" method="POST">
             <label for="username">Username:</label>
             <input type="text" name="username" value="<?php echo htmlspecialchars($username ?? '', ENT_QUOTES, 'UTF-8'); ?>">

             <label for="display_name">Display Name:</label>
             <input type="text" id="display_name" name="display_name" value="<?php echo htmlspecialchars($display_name ?? '', ENT_QUOTES, 'UTF-8'); ?>">

             <label for="password">Password:</label>
             <input type="password" id="password" name="password">

             <label for="password_confirm">Confirm Password:</label>
             <input type="password" id="password_confirm" name="password_confirm">
             <button type="submit">Submit</button>
           </form>
         </td>
       </tr>
     </table>
   </main>

   <?php include __DIR__ . '/footer.php'; ?>

 </body>

 </html>