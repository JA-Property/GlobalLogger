<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;
use App\Controllers\SystemControllers\AppSetupController;

// Attempt to load environment variables from the project root
try {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
} catch (InvalidPathException $e) {
    // If .env file is missing, let the controller handle it.
}

$envController = new AppSetupController();
$envController->checkEnv();

// If .env exists, continue with your normal bootstrapping
echo 'APP_ENV: ' . ($_ENV['APP_ENV'] ?? 'not set') . "\n";


// Continue with application bootstrapping (e.g. logger, etc.)
require_once __DIR__ . '/../app/Core/GlobalLogger.php';
$logger = GlobalLogger::getInstance();

// Test Log Entries
$logger->log(
    GlobalLogger::DEBUG, 
    'AuthModule',
    "Debugging mode activated", 
    ['module' => 'authentication'], 
    ['file' => __FILE__, 'line' => __LINE__]
);

$logger->log(
    GlobalLogger::INFO, 
    'UserModule',
    "User logged in successfully", 
    ['user_id' => 101], 
    ['file' => __FILE__, 'line' => __LINE__]
);

$logger->log(
    GlobalLogger::WARNING, 
    'SystemModule',
    "Low disk space warning", 
    ['disk_space' => '500MB'], 
    ['file' => __FILE__, 'line' => __LINE__]
);

$logger->log(
    GlobalLogger::ERROR, 
    'DatabaseModule',
    "Database connection failed", 
    ['error' => 'Timeout'], 
    ['file' => __FILE__, 'line' => __LINE__]
);

$logger->log(
    GlobalLogger::CRITICAL, 
    'SystemModule',
    "Application crashed due to memory overflow", 
    ['memory' => 'Out of memory'], 
    ['file' => __FILE__, 'line' => __LINE__]
);

