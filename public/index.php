<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\EnvController;

// Check if the .env file exists
$envController = new EnvController();
$envController->checkEnv();

// If .env exists, continue with your normal bootstrapping:
use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;

try {
    // Load environment variables from the project root
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
} catch (InvalidPathException $e) {
    echo 'Warning: .env file not found. Continuing without loading environment variables.' . "\n";
}

// ... Continue with your application (like instantiating GlobalLogger, etc.)
echo 'APP_ENV: ' . ($_ENV['APP_ENV'] ?? 'not set') . "\n";

// Include GlobalLogger (which decides its own log directory)
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

echo "Logs have been written. Check the 'logs' directory.";
?>
