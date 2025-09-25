<?php
// Bootstrap file to set up Laravel paths when running from root directory
define('LARAVEL_START', microtime(true));

// Define the application path
$appPath = __DIR__ . '/Click2Serve';
define('APP_PATH', $appPath);

// Set the working directory to the Laravel root
chdir($appPath);

// Load the Laravel application
require_once $appPath . '/vendor/autoload.php';
