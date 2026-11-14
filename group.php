<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = require_once __DIR__ . '/db.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}



$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
$group_id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

if (!$group_id) {
    http_response_code(404);
    exit('Invalid ID.');
}

    // Fetch group dets
$stmt = $pdo->prepare("SELECT study_groups.*, COUNT(memberships.user_id) AS number_of_member,
   (study_groups.capacity - COUNT(memberships.user_id)) AS places_left
   FROM study_groups
   LEFT JOIN memberships ON study_groups.id = memberships.group_id
   WHERE study_groups.id = ?
   GROUP BY study_groups.id");
$stmt->execute([$group_id]);
$group = $stmt->fetch();

if (!$group) {
    http_response_code(404);
    exit('Group not found');
}
$stmt = $pdo->prepare("SELECT users.display_name FROM memberships
        JOIN users ON memberships.user_id = users.id 
        WHERE memberships.group_id = ?"
        );
$stmt->execute([$group_id]);
$members = $stmt->fetchAll();


    $logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    $user_id = $_SESSION['user_id'] ?? null;

    $is_owner = false;
    $is_member = false;
    $places_Left = (int) $group['places_left'];

    if ($logged_in && $user_id) {
        $is_owner = ((int)$group_id['owner_id'] === (int)$user_id);

            $stmt = $pdo->prepare("SELECT COUNT(*) FROM memberships WHERE group_id = ? AND user_id = ?");
            $stmt->execute([$group_id, $user_id]);
            $is_member = (int)$stmt->fetchColumn() > 0;  

      }

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?=htmlspecialchars($group['title']) ?>Group</title>
</head>
<body>
    <h2><strong>Course:</strong> <?= htmlspecialchars($group['course_code']) ?></h2>
        <p><?= htmlspecialchars($group['description']) ?></p>
     <ul>
        <li><strong>Time:</strong> <?= htmlspecialchars($group['meets_at']) ?></li>
        <li><strong>Capacity:</strong> <?= htmlspecialchars($group['capacity']) ?></li>
        <li><strong>Place:</strong> <?= htmlspecialchars($group['number_of_member']) ?></li>
        <li></strong>Places left:</strong> <?= $places_Left ?></li>
    </ul>   
        <p><strong>Members List</strong></p>
        <ul>
        <?php if (empty($members_list)): ?>
            <li>No members</li>
        <?php else: ?>    
            <li><?= htmlspecialchars($members_list['display_name']) ?></li>       
        <?php endif; ?>     
    </ul>   

    <div>
        <?php if ($error): ?>
            <?php if (is_string($error)): ?>
                <p><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
        <?php endif; ?>         
    </div>
    <div>
            <?php if($logged_in): ?>
            <?php if ($is_owner): ?>
                <p>Owner of the group</p>
            <?php elseif ($is_member): ?>
                <form action="group-leave.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="group_id" value="<?= (int)$group['id'] ?>">
                    <button type="submit">Leave</button>
                </form>
            <?php elseif ($places_Left > 0): ?>
                <form action="group-join.php" method="post">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="group_id" value="<?= (int)$group['id'] ?>">
                        <button type="submit">Join</button>
                </form>        
            <?php else: ?>
                <p class="error">Study group is full.</p> 
            <?php endif; ?>
            <?php else: ?>          
                <p><a href="login.php">Log in</a></p>
            <?php endif; ?>           
</div> 
<?php include __DIR__ . '/footer.php'; ?>       
</body>
</html>