<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
$app = require_once __DIR__.'/../bootstrap/app.php';

// Capture the request
$request = Request::capture();

// Set base path for subdirectory installations (local development)
if (strpos($request->getRequestUri(), '/tajtrainerv2/public') === 0) {
    $request->server->set('SCRIPT_NAME', '/tajtrainerv2/public/index.php');
}

$app->handleRequest($request);
