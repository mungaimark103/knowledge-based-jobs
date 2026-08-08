<?php

$tmpDirs = [
    '/tmp/views',
    '/tmp/sessions',
    '/tmp/cache',
    '/tmp/framework/views',
    '/tmp/framework/sessions',
    '/tmp/framework/cache',
    '/tmp/logs',
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->useStoragePath('/tmp');

// Register ViewServiceProvider explicitly to prevent "Target class [view] does not exist" on early errors
if (!$app->bound('view')) {
    $app->register(\Illuminate\View\ViewServiceProvider::class);
}

try {
    $app->handleRequest(\Illuminate\Http\Request::capture());
} catch (\Throwable $e) {
    if (getenv('APP_DEBUG') === 'true' || env('APP_DEBUG') === true) {
        http_response_code(500);
        header('Content-Type: text/plain');
        echo "Serverless Execution Error:\n";
        echo get_class($e) . ": " . $e->getMessage() . "\n\n";
        echo $e->getTraceAsString();
        exit(1);
    }
    throw $e;
}
