<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

// Diagnostic timing — only when ?perf=1 is on the URL. Measures each
// bootstrap phase BEFORE Laravel middleware starts, so we can see where
// the invisible 1-2s is going.
$__perf = isset($_GET['perf']) && $_GET['perf'];
if ($__perf) { $__t0 = microtime(true); }

require __DIR__.'/../vendor/autoload.php';
if ($__perf) { $__t1 = microtime(true); }

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';
if ($__perf) { $__t2 = microtime(true); }

$kernel = $app->make(Kernel::class);
if ($__perf) { $__t3 = microtime(true); }

$response = $kernel->handle(
    $request = Request::capture()
);
if ($__perf) {
    $__t4 = microtime(true);
    $response->headers->set('X-Boot-Autoload',  (int)(($__t1 - $__t0)*1000) . 'ms');
    $response->headers->set('X-Boot-App',       (int)(($__t2 - $__t1)*1000) . 'ms');
    $response->headers->set('X-Boot-Kernel',    (int)(($__t3 - $__t2)*1000) . 'ms');
    $response->headers->set('X-Boot-Handle',    (int)(($__t4 - $__t3)*1000) . 'ms');
    $response->headers->set('X-Boot-Total',     (int)(($__t4 - $__t0)*1000) . 'ms');
}
$response->send();

$kernel->terminate($request, $response);
