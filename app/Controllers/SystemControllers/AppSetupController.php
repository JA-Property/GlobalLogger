<?php
namespace App\Controllers\SystemControllers;

class AppSetupController {
    /**
     * Checks if a critical environment variable exists. If not, 
     * it displays the env form so the user can generate a .env file.
     */
    public function checkEnv() {
        // Require APP_ENV to be set; if not, render the form.
        if (!isset($_ENV['APP_ENV']) || empty($_ENV['APP_ENV'])) {
            // If the form was submitted, process the input
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->generateEnvFile();
            } else {
                $this->renderEnvForm();
            }
            exit;
        }
    }

    /**
     * Renders the environment file creation form.
     */
    public function renderEnvForm() {
        require_once __DIR__ . '/../../Views/SystemViews/envHelper.php';
    }

    /**
     * Processes the submitted form data and creates a .env file.
     */
    public function generateEnvFile() {
        // Sanitize and set default values
        $appEnv = filter_input(INPUT_POST, 'APP_ENV', FILTER_SANITIZE_STRING) ?: 'development';
        $dbHost = filter_input(INPUT_POST, 'DB_HOST', FILTER_SANITIZE_STRING) ?: 'localhost';
        $dbName = filter_input(INPUT_POST, 'DB_NAME', FILTER_SANITIZE_STRING) ?: 'your_database';
        $dbUsername = filter_input(INPUT_POST, 'DB_USERNAME', FILTER_SANITIZE_STRING) ?: 'root';
        $dbPassword = filter_input(INPUT_POST, 'DB_PASSWORD', FILTER_SANITIZE_STRING) ?: 'your_password';
        $logDirectory = filter_input(INPUT_POST, 'LOG_DIRECTORY', FILTER_SANITIZE_STRING) ?: 'logs';
        $maxLogFileSize = filter_input(INPUT_POST, 'MAX_LOG_FILE_SIZE', FILTER_SANITIZE_NUMBER_INT) ?: 10485760;
    
        // Create the .env content
        $envContent = <<<ENV
    APP_ENV={$appEnv}
    DB_HOST={$dbHost}
    DB_NAME={$dbName}
    DB_USERNAME={$dbUsername}
    DB_PASSWORD={$dbPassword}
    LOG_DIRECTORY={$logDirectory}
    MAX_LOG_FILE_SIZE={$maxLogFileSize}
    ENV;
    
        // Define the file path (assuming project root is one directory up from app/)
        $envFilePath = __DIR__ . '/../../../.env';
    
        // Write the file
        if (file_put_contents($envFilePath, $envContent) !== false) {
            // Redirect to the base URL to re-check the environment file
            header("Location: /");
            exit;
        } else {
            echo "There was an error creating the .env file. Please check permissions.";
        }
    }
    
}
