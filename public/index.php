<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Exception\InvalidPathException;

try {
    // Load environment variables from the root directory (.env file)
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
} catch (InvalidPathException $e) {
    // Gracefully handle the missing .env file
    echo('Warning: .env file not found. Continuing without loading environment variables.') . "\n";
}

// Test print the values with fallback if not set
echo 'APP_ENV: ' . ($_ENV['APP_ENV'] ?? 'not set') . "\n";
echo 'DB_HOST: ' . ($_ENV['DB_HOST'] ?? 'not set') . "\n";
echo 'DB_USERNAME: ' . ($_ENV['DB_USERNAME'] ?? 'not set') . "\n";
echo 'DB_PASSWORD: ' . ($_ENV['DB_PASSWORD'] ?? 'not set') . "\n";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    index
</body>
</html>

<?php
require_once '../app/Core/GlobalLogger.php';

$logger = GlobalLogger::getInstance([
    'logDirectory' => __DIR__ . '/logs',
    'maxFileSize'  => 10485760,
    'db' => [
        'dsn'      => 'mysql:host=localhost;dbname=test_db;charset=utf8',
        'username' => 'root',
        'password' => '',
    ]
]);

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
