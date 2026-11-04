// still editing 

<?php
  session_start();



   $errors = [];
   $username = '';
   // server //
   if ($_SERVER['REQUEST_METHOD'] === 'POST') {

       $display_name = trim($_POST['display_name'] ?? '');
       $username = trim($_POST['username'] ?? '');
       $username = mb_strtolower($username, 'UTF-8');
       $password = $_POST['password'] ?? '';
       $password_confirm = $_POST['password_confirm'] ?? '';  


       // Validate
    if ($username === '') {
        $errors['username'] = 'Username is needed.';
    } elseif (mb_strlen($username,'UTF-8') < 3 || mb_strlen($username, 'UTF-8') > 20) {
       $errors['username'] = 'Username must be between 3 and 20 characters.';
    }
      elseif (!preg_match('/^[a-z0-9_]+$/', $username)) {
          $errors['username'] = 'Username can only contain lowercase letters, digits and underscores';
    } 
    if (mb_strlen($display_name, 'UTF-8') >= 50) {
        $errors['display_name'] = "Input is too long. Maximum input of 50 characters.";
           
    }
    if (mb_strlen($password, 'UTF-8') < 8) {
           $errors['password'] = "Password must be at least 8 characters.";
       
       }

         if  ($password !== $password_confirm) {
        $errors['password'] = "Passwords do not match!";
        }
      
     if (empty($errors)) {


      try {
        
        $pdo = new PDO("mysql:host=localhost;dbname=f6827445_project;charset=utf8mb4", "f6827445_tester1", "tester1");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


      if (count($errors) === 0) {
        $statement = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? LIMIT 1");
        $statement->execute([$username]);
      }
                             
    if ((int)$statement->fetchColumn() > 0) {
                 $errors['username'] = 'Username is already taken.';
        } else { 
          
            $_SESSION['password_temp']= password_hash($password, PASSWORD_DEFAULT);    
            
    
            $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, display_name) VALUES (?, ?, ?)");
            $stmt->execute([$username, $_SESSION['password_temp'], $display_name]);
            
    
            header('Location: index.php', true, 303);
            exit;   
   }
   } catch (PDOException $e) {
            error_log ("Database Error: " . $e->getMessage());
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
</head>
<body>
    <h1>Register</h1>
                          <!-- Register Form error-->
                   <?php if (!empty($errors)): ?>
                        <span style="color: red; font-size: 10px;">
                           <?php echo htmlspecialchars(reset($errors), ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                   <?php endif; ?>           
                   
                   <!-- registration form-->
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
 </body>
</html>                    
