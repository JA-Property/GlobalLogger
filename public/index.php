<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;
use App\Controllers\SystemControllers\AppSetupController;
use App\Core\Router;

// Attempt to load environment variables from the project root
try {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
} catch (InvalidPathException $e) {
    // If .env file is missing, let the controller handle it.
}

$envController = new AppSetupController();
$envController->checkEnv();

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

// Instantiate the router
$router = new Router();

// Define your routes
$router->add('GET', '/', function() {
    // You might check environment or load your dashboard
    $envController = new AppSetupController();
    $envController->checkEnv();
    echo 'Home Page - APP_ENV: ' . ($_ENV['APP_ENV'] ?? 'not set');
});

// Add other routes, e.g., for CRM, billing, scheduling
$router->add('GET', '/crm', function() {
    // Route to your CRM controller/action
    echo "CRM Module";
});

// Dispatch the current request
$router->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));