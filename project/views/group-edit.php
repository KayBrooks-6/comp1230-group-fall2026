<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/csrf.php';
requireCsrf();

if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php', true, 303);
    exit;
}

$errors = [];

try {
    $pdo = require __DIR__ . '/db.php';
} catch (PDOException $e) {
    error_log('Database Error: ' . $e->getMessage());
    http_response_code(500);
    exit('<p class="error">Database error.</p>');
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(404);
    exit('<p class="error">Study group not found.</p>');
}

try {
    $statement = $pdo->prepare('SELECT * FROM study_groups WHERE id = :id');
    $statement->execute([':id' => $id]);
    $group = $statement->fetch();
} catch (PDOException $e) {
    error_log('Database Error: ' . $e->getMessage());
    http_response_code(500);
    exit('<p class="error">Database error.</p>');
}

if (!$group) {
    http_response_code(404);
    exit('<p class="error">Study group not found.</p>');
}

if ((int)$group['owner_id'] !== (int)$_SESSION['user']['id']) {
    http_response_code(403);
    exit('<p class="error">You can only change your own study groups.</p>');
}

$course_code = $group['course_code'];
$title = $group['title'];
$description = $group['description'];
$meets_at = str_replace(' ', 'T', substr($group['meets_at'], 0, 16));
$location = $group['location'];
$capacity = (string)$group['capacity'];

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

    if (!preg_match('/^[0-9]+$/D', $capacity) || (int)$capacity < 2 || (int)$capacity > 10) {
        $errors[] = 'Capacity must be a whole number between 2 and 10.';
    }

    $validDateTime = false;

    if (preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})T([0-9]{2}):([0-9]{2})$/D', $meets_at, $parts)) {
        $year = (int)$parts[1];
        $month = (int)$parts[2];
        $day = (int)$parts[3];
        $hour = (int)$parts[4];
        $minute = (int)$parts[5];

        if (strlen($meets_at) === 16 && checkdate($month, $day, $year) && $hour >= 0 && $hour <= 23 && $minute >= 0 && $minute <= 59) {
            $validDateTime = true;
        }
    }

    if (!$validDateTime) {
        $errors[] = 'Please enter a valid meeting date and time.';
    }

    if (empty($errors)) {
        try {
            $forDatabase = str_replace('T', ' ', $meets_at) . ':00';

            $statement = $pdo->prepare("
                UPDATE study_groups
                SET course_code = :course_code,
                    title = :title,
                    description = :description,
                    meets_at = :meets_at,
                    location = :location,
                    capacity = :capacity
                WHERE id = :id AND owner_id = :owner_id
            ");

            $statement->execute([
                ':course_code' => $course_code,
                ':title' => $title,
                ':description' => $description,
                ':meets_at' => $forDatabase,
                ':location' => $location,
                ':capacity' => (int)$capacity,
                ':id' => $id,
                ':owner_id' => (int)$_SESSION['user']['id'],
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Study Group</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <?php include __DIR__ . '/header.php'; ?>

    <main class="container py-4" style="max-width: 680px;">
        <article class="card shadow-sm">
            <header class="card-header bg-white">
                <h1 class="h3 mb-0">Edit Study Group</h1>
            </header>

            <section class="card-body">
                <?php if (!empty($errors)): ?>
                    <section class="error alert alert-danger" role="alert">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                <?php endif; ?>

                <form action="group-edit.php?id=<?= (int)$id ?>" method="POST">
                    <?= csrfField() ?>

                    <p class="mb-3">
                        <label for="course_code" class="form-label">Course Code:</label>
                        <input type="text" id="course_code" name="course_code" class="form-control" maxlength="8" value="<?= htmlspecialchars($course_code, ENT_QUOTES, 'UTF-8') ?>" required>
                    </p>

                    <p class="mb-3">
                        <label for="title" class="form-label">Title:</label>
                        <input type="text" id="title" name="title" class="form-control" minlength="3" maxlength="80" value="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>" required>
                    </p>

                    <p class="mb-3">
                        <label for="description" class="form-label">Description:</label>
                        <textarea id="description" name="description" class="form-control" maxlength="1000" rows="5" required><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?></textarea>
                    </p>

                    <p class="mb-3">
                        <label for="meets_at" class="form-label">Meeting Date and Time:</label>
                        <input type="datetime-local" id="meets_at" name="meets_at" class="form-control" value="<?= htmlspecialchars($meets_at, ENT_QUOTES, 'UTF-8') ?>" required>
                    </p>

                    <p class="mb-3">
                        <label for="location" class="form-label">Location:</label>
                        <input type="text" id="location" name="location" class="form-control" maxlength="80" value="<?= htmlspecialchars($location, ENT_QUOTES, 'UTF-8') ?>" required>
                    </p>

                    <p class="mb-4">
                        <label for="capacity" class="form-label">Capacity:</label>
                        <input type="number" id="capacity" name="capacity" class="form-control" min="2" max="10" step="1" value="<?= htmlspecialchars($capacity, ENT_QUOTES, 'UTF-8') ?>" required>
                    </p>

                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="index.php" class="btn btn-outline-secondary ms-2">Cancel</a>
                </form>
            </section>
        </article>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>
</body>

</html>