<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit;
}
require_once __DIR__ . '/csrf.php';
requireCsrf();

if (empty($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit('Logged in users only');
}


$user_id = (int)$_SESSION['user']['id'];
$group_id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

if (!$group_id) {
    http_response_code(404);
    exit('Invalid group ID.');
    }
 $pdo = require_once __DIR__ . '/db.php';

$stmt = $pdo->prepare("SELECT capacity FROM study_groups WHERE id = ?");
$stmt->execute([$group_id]);
$group = $stmt->fetch();

 if (!$group) {
    http_response_code(404);
    exit('Group not found.');
}    
$stmtUser = $pdo->prepare("SELECT COUNT(*) FROM memberships WHERE group_id = ? AND user_id = ?");
$stmtUser->execute([$group_id, $user_id]);
$user = $stmtUser->fetchColumn();
 if ($user) {
    header("Location: group.php?id=" . $group_id);
    exit('You are already a member.');
}  
$capacity = (int)$group['capacity'];     

     $countStmt = $pdo->prepare("SELECT COUNT(*) FROM memberships WHERE group_id = ?");
     $countStmt->execute([$group_id]);
     $memberCount = $countStmt->fetchColumn();

if ($memberCount >= $group['capacity']) {
    $_SESSION['error'] = "This study group is full.";
    header("Location: group.php?id=" . $group_id);
    exit;
}

try {
     $stmt = $pdo->prepare("INSERT INTO memberships (group_id, user_id) VALUES (?, ?)");
     $stmt->execute([$group_id, $user_id]);
     $_SESSION['success'] = "Joined group.";
} catch (PDOException $e) {
   if (isset($e->errorInfo[1]) && (int)$e->errorInfo[1] === 1062) {
    $_SESSION['error'] = "You are already a member of this group.";
    } else {
        $_SESSION['error'] = "Could not join gorup.";
    }
}
    header("Location: group.php?id=" . $group_id);
    exit;
?>

