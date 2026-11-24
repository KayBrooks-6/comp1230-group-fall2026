<?php
// src/Controllers/PageController.php

namespace App\Controllers;

use App\View;

class PageController
{
    public function __construct(private View $view) {}

    // GET /team
    public function team(): void
    {
        // TODO: pass team info / members data and render 'team' inside layout
    }
}