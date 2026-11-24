<?php
$file = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (PHP_SAPI === 'cli-server' && is_file($file) && basename($file) !== 'index.php') {
    return false;
}

require __DIR__ . '/../bootstrap.php';

use App\Controllers\AuthController;
use App\Controllers\GroupController;
use App\Controllers\PageController;
use App\Models\GroupModel;
use App\Models\UserModel;
use App\View;

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';

$script = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
if (basename($script) === 'public' && !str_starts_with($path . '/', $script . '/')) {
    $script = rtrim(dirname($script), '/');
}
define('BASE', $script);

$route  = '/' . trim(substr($path, strlen(BASE)), '/');
$parts  = $route === '/' ? [] : explode('/', trim($route, '/'));
$method = $_SERVER['REQUEST_METHOD'];

$pdo = db(); // or whatever function/return your lib/db.php uses
$view = new View(__DIR__ . '/../views');
$groupModel = new GroupModel($pdo);
$userModel = new UserModel($pdo);
$groups = new GroupController($groupModel, $view);
$auth = new AuthController($userModel, $view);
$pages = new PageController($view);

$isGroupId = static fn(array $p): bool => count($p) >= 2 && $p[0] === 'groups' && ctype_digit($p[1]);

match (true) {
    $method === 'GET' && $parts === [] => $groups->index(),

    $method === 'GET' && $parts === ['register'] => $auth->registerForm(),
    $method === 'POST' && $parts === ['register'] => $auth->register(),

    $method === 'GET' && $parts === ['login'] => $auth->loginForm(),
    $method === 'POST' && $parts === ['login'] => $auth->login(),

    $method === 'POST' && $parts === ['logout'] => $auth->logout(),

    $method === 'GET' && $parts === ['groups', 'add'] => $groups->createForm(),
    $method === 'POST' && $parts === ['groups', 'add'] => $groups->create(),

    $method === 'GET' && count($parts) === 2 && $parts[0] === 'groups' && ctype_digit($parts[1]) => $groups->show((int) $parts[1]),

    $method === 'GET' && count($parts) === 3 && $parts[0] === 'groups' && ctype_digit($parts[1]) && $parts[2] === 'edit' => $groups->editForm((int) $parts[1]),
    $method === 'POST' && count($parts) === 3 && $parts[0] === 'groups' && ctype_digit($parts[1]) && $parts[2] === 'edit' => $groups->update((int) $parts[1]),

    $method === 'POST' && count($parts) === 3 && $parts[0] === 'groups' && ctype_digit($parts[1]) && $parts[2] === 'delete' => $groups->delete((int) $parts[1]),
    $method === 'POST' && count($parts) === 3 && $parts[0] === 'groups' && ctype_digit($parts[1]) && $parts[2] === 'join' => $groups->join((int) $parts[1]),
    $method === 'POST' && count($parts) === 3 && $parts[0] === 'groups' && ctype_digit($parts[1]) && $parts[2] === 'leave' => $groups->leave((int) $parts[1]),
    $method === 'GET' && count($parts) === 3 && $parts[0] === 'groups' && ctype_digit($parts[1]) && $parts[2] === 'members.csv' => $groups->membersCsv((int) $parts[1]),

    $method === 'GET' && $parts === ['my-groups'] => $groups->myGroups(),
    $method === 'GET' && $parts === ['team'] => $pages->team(),
    $method === 'GET' && $parts === ['api', 'groups', 'upcoming'] => $groups->upcomingJson(),

    default => notFound(),
};
