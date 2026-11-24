<?php
// src/Controllers/AuthController.php

namespace App\Controllers;

use App\Models\UserModel;
use App\View;

class AuthController
{
    public function __construct(
        private UserModel $users,
        private View $view
    ) {}

    // GET /register
    public function registerForm(): void
    {
        // TODO: render 'auth/register' with empty fields and no errors
    }

    // POST /register
    public function register(): void
    {
        // TODO: read & trim inputs, validate fields/passwords, check duplicate username,
        // create user, log them in (session_regenerate_id(true) + set $_SESSION['user']), redirect to /
    }

    // GET /login
    public function loginForm(): void
    {
        // TODO: render 'auth/login' with empty username and no error
    }

    // POST /login
    public function login(): void
    {
        // TODO: read & trim username/password, verify credentials via $this->users,
        // regenerate session id, store user in $_SESSION, redirect to / (or show error on failure)
    }

    // POST /logout
    public function logout(): void
    {
        // TODO: clear session data/cookie, destroy session, redirect to /
    }
}