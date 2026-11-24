<?php
// src/Models/UserModel.php

namespace App\Models;

use PDO;

class UserModel
{
    public function __construct(private PDO $pdo) {}

    public function findByUsername(string $username): ?array
    {
        // TODO: prepared SELECT from users WHERE username = ?; return row array or null
        return null;
    }

    public function findById(int $id): ?array
    {
        // TODO: prepared SELECT from users WHERE id = ?; return row array or null
        return null;
    }

    public function validateRegistration(array $input): array
    {
        // TODO: validate username, display_name, password, password_confirm; return array of errors ([] if valid)
        return [];
    }

    public function create(string $username, string $displayName, string $plainPassword): int
    {
        // TODO: hash password with password_hash(), prepared INSERT into users, return new user id
        return 0;
    }

    public function authenticate(string $username, string $plainPassword): ?array
    {
        // TODO: look up user by username and verify password with password_verify(); return user array or null
        return null;
    }
}