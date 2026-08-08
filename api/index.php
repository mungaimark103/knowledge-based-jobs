<?php

// Ensure serverless writable directories exist in /tmp
$tmpDirs = [
    '/tmp/views',
    '/tmp/sessions',
    '/tmp/cache',
    '/tmp/framework/views',
    '/tmp/framework/sessions',
    '/tmp/framework/cache',
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Forward all incoming Vercel serverless requests to public/index.php
require __DIR__ . '/../public/index.php';
