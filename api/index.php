<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$storagePath = '/tmp/laravel';

if (! is_dir($storagePath)) {
    if (! mkdir($storagePath, 0755, true) && ! is_dir($storagePath)) {
        throw new RuntimeException('Unable to create the temporary Laravel storage directory.');
    }
}

require __DIR__.'/../vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->useStoragePath($storagePath);

$app->handleRequest(Request::capture());
