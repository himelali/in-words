<?php

/*
 * Uses Composer's autoloader when installed, otherwise a minimal PSR-4 loader,
 * so the tests also run without "composer install" on any PHP version.
 */

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

$composer = __DIR__ . '/../vendor/autoload.php';
if (is_file($composer)) {
    require $composer;
    return;
}

spl_autoload_register(function ($class) {
    // Most specific prefix first, same mapping as composer.json.
    $prefixes = [
        'Himelali\\Tests\\' => __DIR__ . '/',
        'Himelali\\' => __DIR__ . '/../src/',
    ];
    foreach ($prefixes as $prefix => $directory) {
        if (strpos($class, $prefix) === 0) {
            $file = $directory . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
            if (is_file($file)) {
                require $file;
            }
            return;
        }
    }
});
