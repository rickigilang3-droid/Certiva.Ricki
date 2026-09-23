<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        http_response_code(500);
        header('Content-Type: text/plain');
        echo "SHUTDOWN FATAL ERROR:\n";
        print_r($error);
    }
});

// Prepare writable serverless storage folders in /tmp
$storageDirs = [
    '/tmp/storage/app/public',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
];

foreach ($storageDirs as $dir) {
    if (! is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Point Laravel storage and view compilation to writable /tmp
putenv('APP_STORAGE=/tmp/storage');
$_ENV['APP_STORAGE'] = '/tmp/storage';

putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

// Copy bundled SQLite database to /tmp if not already present and no external DB configured
$tmpDb = '/tmp/database.sqlite';
$bundledDb = __DIR__.'/../database/database.sqlite';

if (! file_exists($tmpDb)) {
    if (file_exists($bundledDb) && filesize($bundledDb) > 0) {
        copy($bundledDb, $tmpDb);
    } else {
        touch($tmpDb);
    }
}

if (! getenv('DB_HOST') && ! getenv('DATABASE_URL')) {
    putenv('DB_CONNECTION=sqlite');
    $_ENV['DB_CONNECTION'] = 'sqlite';
    putenv("DB_DATABASE={$tmpDb}");
    $_ENV['DB_DATABASE'] = $tmpDb;
}

// Fallback encryption key if not provided in Vercel dashboard
if (! getenv('APP_KEY') && ! isset($_ENV['APP_KEY'])) {
    $defaultKey = 'base64:+Vp7JaY4goZkPGrdAXd6E/kii7q9uMcIlZyvFwdwC2c=';
    putenv("APP_KEY={$defaultKey}");
    $_ENV['APP_KEY'] = $defaultKey;
}

if (! getenv('APP_DEBUG') && ! isset($_ENV['APP_DEBUG'])) {
    putenv('APP_DEBUG=true');
    $_ENV['APP_DEBUG'] = 'true';
}

if (! getenv('SESSION_DRIVER') && ! isset($_ENV['SESSION_DRIVER'])) {
    putenv('SESSION_DRIVER=cookie');
    $_ENV['SESSION_DRIVER'] = 'cookie';
}

if (! getenv('CACHE_STORE') && ! isset($_ENV['CACHE_STORE'])) {
    putenv('CACHE_STORE=array');
    $_ENV['CACHE_STORE'] = 'array';
}

if (! getenv('LOG_CHANNEL') && ! isset($_ENV['LOG_CHANNEL'])) {
    putenv('LOG_CHANNEL=stderr');
    $_ENV['LOG_CHANNEL'] = 'stderr';
}

try {
    require __DIR__.'/../public/index.php';
} catch (Throwable $e) {
    http_response_code(500);
    echo '<h1>Certiva Exception Caught</h1>';
    echo '<p><strong>Message:</strong> '.htmlspecialchars($e->getMessage()).'</p>';
    echo '<p><strong>File:</strong> '.htmlspecialchars($e->getFile()).':'.$e->getLine().'</p>';
    echo '<pre>'.htmlspecialchars($e->getTraceAsString()).'</pre>';
}
