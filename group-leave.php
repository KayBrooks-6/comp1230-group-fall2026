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

$logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$user_id = $_SESSION['user']['id'] ?? null;
$group_id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);



if (!$logged_in) {
    header("Location: login.php");
    exit;
}

if (!$group_id) {
    http_response_code(404);
    exit('Invalid ID.');
}
$pdo = require_once __DIR__ . '/db.php';

$stmt = $pdo->prepare("SELECT study_groups.*, users.display_name AS owner_name,
  COUNT(memberships.user_id) AS number_of_member,
   (study_groups.capacity - COUNT(memberships.user_id)) AS places_left
   FROM study_groups
   LEFT JOIN users ON study_groups.owner_id = users.id
   LEFT JOIN memberships ON study_groups.id = memberships.group_id
   WHERE study_groups.id = ?
   GROUP BY study_groups.id, users.display_name");
$stmt->execute([$group_id]);
$group = $stmt->fetch();

if (!$group) {
    http_response_code(404);
    exit('Group not found.');
}



$is_member = false;
$is_owner = false;
$stmt = $pdo->prepare("SELECT COUNT(*) FROM memberships WHERE group_id = ? AND user_id = ?");
$stmt->execute([$group_id, $user_id]);
$is_member = (int)$stmt->fetchColumn() > 0;   
  
if (!$is_member) {
    $_SESSION['error'] = "Not a member of this group.";
    header("Location: group.php?id=" . $group_id);
    exit;
}

$is_owner = ((int)$group['owner_id'] === (int)$user);
if ($is_owner) {
    $_SESSION['error'] = "Owner can't leave group.";
    header("Location: group.php?id=" . $group_id);
    exit;
}   
$stmtUser = $pdo->prepare("DELETE FROM memberships WHERE group_id = ? AND user_id = ?");
$stmtUser->execute([$group_id, $user_id]);

    header("Location: group.php?id=" . $group_id);
    exit;

?>