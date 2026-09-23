<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\ApplicationBuilder;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

$app = new Application(dirname(__DIR__));

if ($storagePath = getenv('APP_STORAGE') ?: ($_ENV['APP_STORAGE'] ?? null)) {
    $app->useStoragePath($storagePath);
}

if (isset($_ENV['VERCEL']) || getenv('VERCEL') || getenv('APP_STORAGE')) {
    $storage = $app->storagePath();
    $cacheDir = $storage.'/framework/cache';
    if (! is_dir($cacheDir)) {
        @mkdir($cacheDir, 0755, true);
    }

    putenv("APP_PACKAGES_CACHE={$cacheDir}/packages.php");
    $_ENV['APP_PACKAGES_CACHE'] = "{$cacheDir}/packages.php";

    putenv("APP_SERVICES_CACHE={$cacheDir}/services.php");
    $_ENV['APP_SERVICES_CACHE'] = "{$cacheDir}/services.php";
}

return (new ApplicationBuilder($app))
    ->withKernels()
    ->withEvents()
    ->withCommands()
    ->withProviders()
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
