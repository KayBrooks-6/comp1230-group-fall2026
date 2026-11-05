<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header('Location: login.php', true, 303);
    exit;
}

$errors = [];

$course_code = '';
$title = '';
$description = '';
$meets_at = '';
$location = '';
$capacity = '';

try {
    $pdo = require __DIR__ . '/db.php';
} catch (PDOException $e) {
    error_log('Database Error: ' . $e->getMessage());
    exit('Database error.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $course_code = trim($_POST['course_code'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $meets_at = trim($_POST['meets_at'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $capacity = trim($_POST['capacity'] ?? '');

    if (!preg_match('/^[A-Za-z]{4}[0-9]{4}$/D', $course_code)) {
        $errors[] = 'Course code must contain four letters followed by four digits, such as COMP1230.';
    } else {
        $course_code = strtoupper($course_code);
    }

    $titleLength = mb_strlen($title, 'UTF-8');

    if ($titleLength < 3 || $titleLength > 80) {
        $errors[] = 'Title must be between 3 and 80 characters.';
    }

    if ($description === '' || mb_strlen($description, 'UTF-8') > 1000) {
        $errors[] = 'Description is required and cannot exceed 1000 characters.';
    }

    if ($location === '' || mb_strlen($location, 'UTF-8') > 80) {
        $errors[] = 'Location is required and cannot exceed 80 characters.';
    }

    if (
        !preg_match('/^[0-9]+$/D', $capacity) ||
        (int)$capacity < 2 ||
        (int)$capacity > 10
    ) {
        $errors[] = 'Capacity must be a whole number between 2 and 10.';
    }

    $validDateTime = false;

    if (
        preg_match(
            '/^([0-9]{4})-([0-9]{2})-([0-9]{2})T([0-9]{2}):([0-9]{2})$/D',
            $meets_at,
            $parts
        )
    ) {
        $year = (int)$parts[1];
        $month = (int)$parts[2];
        $day = (int)$parts[3];
        $hour = (int)$parts[4];
        $minute = (int)$parts[5];

        if (
            strlen($meets_at) === 16 &&
            checkdate($month, $day, $year) &&
            $hour >= 0 && $hour <= 23 &&
            $minute >= 0 && $minute <= 59
        ) {
            $validDateTime = true;
        }
    }

    if (!$validDateTime) {
        $errors[] = 'Please enter a valid meeting date and time.';
    }

    if (empty($errors)) {
        try {
            $forDatabase = str_replace('T', ' ', $meets_at) . ':00';

            $statement = $pdo->prepare(
                "INSERT INTO study_groups
                (owner_id, course_code, title, description, meets_at, location, capacity)
                VALUES
                (:owner_id, :course_code, :title, :description, :meets_at, :location, :capacity)"
            );

            $statement->execute([
                ':owner_id' => $_SESSION['user']['id'],
                ':course_code' => $course_code,
                ':title' => $title,
                ':description' => $description,
                ':meets_at' => $forDatabase,
                ':location' => $location,
                ':capacity' => (int)$capacity
            ]);

            header('Location: index.php', true, 303);
            exit;

        } catch (PDOException $e) {
            $errors[] = 'Database error.';
            error_log('Database Error: ' . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Create Study Group</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<header>
    <nav>
        <a href="index.php">Home</a> |
        <a href="team.php">Team</a>
        | Logged in as <?= htmlspecialchars($_SESSION['user']['display_name'], ENT_QUOTES, 'UTF-8') ?>
        | <a href="group-create.php">Create Group</a>

        <form action="logout.php" method="POST" style="display:inline;">
            <button type="submit">Log out</button>
        </form>
    </nav>
</header>

<main>
    <h1>Create Study Group</h1>

    <?php if (!empty($errors)): ?>
        <div style="color: red;">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="group-create.php" method="POST">

        <p>
            <label for="course_code">Course Code:</label><br>
            <input type="text" id="course_code" name="course_code"
                maxlength="8" required
                value="<?= htmlspecialchars($course_code, ENT_QUOTES, 'UTF-8') ?>">
        </p>

        <p>
            <label for="title">Title:</label><br>
            <input type="text" id="title" name="title"
                minlength="3" maxlength="80" required
                value="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>">
        </p>

        <p>
            <label for="description">Description:</label><br>
            <textarea id="description" name="description"
                maxlength="1000" required rows="5" cols="40"><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?></textarea>
        </p>

        <p>
            <label for="meets_at">Meeting Date and Time:</label><br>
            <input type="datetime-local" id="meets_at" name="meets_at"
                required
                value="<?= htmlspecialchars($meets_at, ENT_QUOTES, 'UTF-8') ?>">
        </p>

        <p>
            <label for="location">Location:</label><br>
            <input type="text" id="location" name="location"
                maxlength="80" required
                value="<?= htmlspecialchars($location, ENT_QUOTES, 'UTF-8') ?>">
        </p>

        <p>
            <label for="capacity">Capacity:</label><br>
            <input type="number" id="capacity" name="capacity"
                min="2" max="10" step="1" required
                value="<?= htmlspecialchars($capacity, ENT_QUOTES, 'UTF-8') ?>">
        </p>

        <button type="submit">Create Group</button>
        <a href="index.php">Cancel</a>

    </form>
</main>

</body>
</html>