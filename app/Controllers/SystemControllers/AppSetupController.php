<?php
namespace App\Controllers\SystemControllers;

class AppSetupController {
    /**
     * Checks if the .env file exists in the project root.
     * If not, renders a view with instructions to create one.
     */
    public function checkEnv() {
        // Assume .env is located in the project root
        $envFilePath = __DIR__ . '/../../.env';

        if (!file_exists($envFilePath)) {
            // Render view to help user generate the .env file
            require_once __DIR__ . '/../Views/SystemViews/envHelper.php';
            exit;
        } else {
            // Optionally, redirect to home or return a success message.
            echo ".env file exists.";
        }
    }
}
