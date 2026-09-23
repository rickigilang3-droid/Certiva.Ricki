<?php

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
if (! getenv('DB_HOST') && ! getenv('DATABASE_URL')) {
    $tmpDb = '/tmp/database.sqlite';
    $bundledDb = __DIR__.'/../database/database.sqlite';

    if (! file_exists($tmpDb) && file_exists($bundledDb)) {
        copy($bundledDb, $tmpDb);
    }

    if (file_exists($tmpDb)) {
        putenv('DB_CONNECTION=sqlite');
        $_ENV['DB_CONNECTION'] = 'sqlite';
        putenv("DB_DATABASE={$tmpDb}");
        $_ENV['DB_DATABASE'] = $tmpDb;
    }
}

// Fallback encryption key if not provided in Vercel dashboard
if (! getenv('APP_KEY') && ! isset($_ENV['APP_KEY'])) {
    $defaultKey = 'base64:+Vp7JaY4goZkPGrdAXd6E/kii7q9uMcIlZyvFwdwC2c=';
    putenv("APP_KEY={$defaultKey}");
    $_ENV['APP_KEY'] = $defaultKey;
}

// Set standard serverless env fallbacks
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

// Forward execution to Laravel's public entrypoint
require __DIR__.'/../public/index.php';

