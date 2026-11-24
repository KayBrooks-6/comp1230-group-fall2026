<?php
// lib/helpers.php

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function url(string $path): string
{
    $normalized = '/' . ltrim($path, '/');
    return (defined('BASE') ? BASE : '') . ($normalized === '/index.php' ? '/index.php' : ($path === '/' ? '/' : $normalized));
}

function redirect(string $path, int $status = 303): never
{
    $target = $path === '/' ? ((defined('BASE') && BASE !== '' ? BASE : '') . '/') : url($path);
    header('Location: ' . $target, true, $status);
    exit;
}

function notFound(string $message = 'Page not found.'): never
{
    http_response_code(404);
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>404 Not Found</title></head><body><h1>404 Not Found</h1><p>' . e($message) . '</p></body></html>';
    exit;
}