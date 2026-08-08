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
        @mkdir($dir, 0777, true);
    }
}

putenv('VIEW_COMPILED_PATH=/tmp/framework/views');
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/framework/views';
$_SERVER['VIEW_COMPILED_PATH'] = '/tmp/framework/views';

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->useStoragePath('/tmp');

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
