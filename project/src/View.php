<?php
// src/View.php

namespace App;

class View
{
    public function __construct(private string $folder) {}

    public function show(string $name, array $data = []): void
    {
        extract($data, EXTR_SKIP);

        ob_start();
        require $this->folder . '/' . ltrim($name, '/') . '.php';
        $content = ob_get_clean();

        require $this->folder . '/layout.php';
    }
}