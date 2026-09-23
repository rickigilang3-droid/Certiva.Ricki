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

// Normalize HTTPS server variables for reverse proxy (Vercel)
if ((isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') || (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on')) {
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['SERVER_PORT'] = '443';
}

// Direct static file delivery for public assets (Vite CSS, JS, images, icons)
$rawUri = $_SERVER['REQUEST_URI'] ?? '/';
$uri = parse_url($rawUri, PHP_URL_PATH);
$publicFile = __DIR__.'/../public'.$uri;
if ($uri !== '/' && file_exists($publicFile) && is_file($publicFile)) {
    $ext = pathinfo($publicFile, PATHINFO_EXTENSION);
    $mimes = [
        'css' => 'text/css; charset=utf-8',
        'js' => 'application/javascript; charset=utf-8',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'json' => 'application/json; charset=utf-8',
        'woff2' => 'font/woff2',
        'woff' => 'font/woff',
        'ttf' => 'font/ttf',
    ];
    $contentType = $mimes[$ext] ?? 'application/octet-stream';
    header("Content-Type: {$contentType}");
    header('Cache-Control: public, max-age=31536000, immutable');
    header('Content-Length: '.filesize($publicFile));
    readfile($publicFile);
    exit;
}

// Prepare writable serverless storage folders in /tmp
$storageDirs = [
    '/tmp/storage/app/public',
    '/tmp/storage/app/public/certificates',
    '/tmp/storage/fonts',
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

putenv('APP_PACKAGES_CACHE=/tmp/storage/framework/cache/packages.php');
$_ENV['APP_PACKAGES_CACHE'] = '/tmp/storage/framework/cache/packages.php';

putenv('APP_SERVICES_CACHE=/tmp/storage/framework/cache/services.php');
$_ENV['APP_SERVICES_CACHE'] = '/tmp/storage/framework/cache/services.php';

putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

// Copy bundled SQLite database to /tmp if not already present or if bundle changed
$tmpDb = '/tmp/database.sqlite';
$bundledDb = __DIR__.'/../database/database.sqlite';
$hashFile = '/tmp/database.sqlite.hash';

if (file_exists($bundledDb) && filesize($bundledDb) > 0) {
    $bundleHash = md5_file($bundledDb);
    $currentHash = file_exists($hashFile) ? trim(file_get_contents($hashFile)) : '';

    if (! file_exists($tmpDb) || $bundleHash !== $currentHash) {
        copy($bundledDb, $tmpDb);
        file_put_contents($hashFile, $bundleHash);
    }
} elseif (! file_exists($tmpDb)) {
    touch($tmpDb);
}

if (! getenv('DB_HOST') && ! getenv('DATABASE_URL')) {
    putenv('DB_CONNECTION=sqlite');
    $_ENV['DB_CONNECTION'] = 'sqlite';
    putenv("DB_DATABASE={$tmpDb}");
    $_ENV['DB_DATABASE'] = $tmpDb;
}

// Fallback encryption key if not provided in Vercel dashboard
$appKey = getenv('APP_KEY') ?: ($_ENV['APP_KEY'] ?? null);
if (! $appKey || trim($appKey) === '') {
    $appKey = 'base64:+Vp7JaY4goZkPGrdAXd6E/kii7q9uMcIlZyvFwdwC2c=';
}
putenv("APP_KEY={$appKey}");
$_ENV['APP_KEY'] = $appKey;

if (! getenv('APP_NAME') || getenv('APP_NAME') === '') {
    putenv('APP_NAME=Certiva');
    $_ENV['APP_NAME'] = 'Certiva';
}

if (! getenv('APP_DEBUG') && ! isset($_ENV['APP_DEBUG'])) {
    putenv('APP_DEBUG=true');
    $_ENV['APP_DEBUG'] = 'true';
}

if (! getenv('SESSION_DRIVER') || getenv('SESSION_DRIVER') === '') {
    putenv('SESSION_DRIVER=cookie');
    $_ENV['SESSION_DRIVER'] = 'cookie';
}

if (! getenv('SESSION_LIFETIME') || getenv('SESSION_LIFETIME') === '') {
    putenv('SESSION_LIFETIME=120');
    $_ENV['SESSION_LIFETIME'] = '120';
}

if (! getenv('BCRYPT_ROUNDS') || getenv('BCRYPT_ROUNDS') === '') {
    putenv('BCRYPT_ROUNDS=12');
    $_ENV['BCRYPT_ROUNDS'] = '12';
}

if (! getenv('CACHE_STORE') && ! isset($_ENV['CACHE_STORE'])) {
    putenv('CACHE_STORE=array');
    $_ENV['CACHE_STORE'] = 'array';
}

if (! getenv('LOG_CHANNEL') && ! isset($_ENV['LOG_CHANNEL'])) {
    putenv('LOG_CHANNEL=stderr');
    $_ENV['LOG_CHANNEL'] = 'stderr';
}

if (! getenv('APP_MAINTENANCE_DRIVER') || getenv('APP_MAINTENANCE_DRIVER') === '') {
    putenv('APP_MAINTENANCE_DRIVER=file');
    $_ENV['APP_MAINTENANCE_DRIVER'] = 'file';
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
