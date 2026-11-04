// still editing 
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

 $errors = [];

try {
        $pdo = new PDO("mysql:host=localhost;dbname=f6827445_project;charset=utf8mb4", "f6827445_tester1", "tester1");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
}  catch (PDOException $e) {
        $errors[] = "Database error.";    
        error_log ("Database Error: " . $e->getMessage());    
    }

 $accountCheck = ['tester1' => 'Study-1230',
                   'tester2' => 'Study-1230'
                   ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {   
     
        $password = $_POST['password'] ?? '';
        $username = trim((string)($_POST['username'] ?? ''));
    
     
    if (array_key_exists($username, $accountCheck) && password_verify($password, $accountCheck[$username])) {
         session_regenerate_id(true);
        $_SESSION['logged_in'] = true;
        // $_SESSION['user_id'] = -1;
        // $_SESSION['username'] = $username;
        //  $_SESSION['display_name'] = $user['username'];
        $_SESSION['last_seen'] = time();
        $_SESSION['user'] = [
             'id'    => $user['id'],
             'username'  => $user['username'],
             'display_name' => $user['username']
];

          header('Location: index.php', true, 303);   
          exit;
    }
          $user = null;

    if (!$errors) {
        try {      
        $statement = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = :username LIMIT 1");
        $statement->execute([':username' => $username]);
        $user = $statement->fetch(PDO::FETCH_ASSOC);     
    } catch (PDOException $e) {
        $errors[] = "Database error.";
        error_log("Database error: " . $e->getMessage());
    }
    }
    if (!$errors) {
        if ($user === null || $user === false || !password_verify($password, $user['password_hash'])) {
        $errors[] = "Incorrect username or password.";
    
    } else {
         session_regenerate_id(true);
        $_SESSION['logged_in'] = true;
        // $_SESSION['user_id'] = $user['id'];
        // $_SESSION['username'] = $user['username'];
        // $_SESSION['display_name'] = $user['username'];
        $_SESSION['last_seen'] = time();  
        $_SESSION['user'] = [
             'id'    => $user['id'],
             'username'  => $user['username'],
             'display_name' => $user['username']
];   

          header("Location: index.php");   
          exit;
        }
    }
    }


?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Login</title>
</head>
<body>
    <div>
    <h2>Login Page</h2>

    <?php if (!empty($errors)): ?>
        <div>
            <?php foreach ($errors as $error): ?>
                <?php if (is_string($error)): ?>
                    <p style="color: red;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?>             
            <?php endforeach; ?>
            </div> 
    <?php endif; ?>
    </div>
              
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

 </body>
</html>         
