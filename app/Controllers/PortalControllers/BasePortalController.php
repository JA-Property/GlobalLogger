<?php

// File: app/Controllers/BasePortalController.php
namespace App\Controllers\PortalControllers;

class BasePortalController {
    protected $portal;
    protected $layout;
    protected $user; // The current user (or impersonated user)

    public function __construct(string $portal) {
        $this->portal = $portal;
        // Example: load user info from session (including impersonation data)
        $this->user = $this->getCurrentUser();
        $this->setLayout();
    }

    // Simulated user loader. Replace with your actual user retrieval logic.
    protected function getCurrentUser() {
        // E.g., $_SESSION['user'] could be an object/array with a 'type'
        $user = $_SESSION['user'] ?? null;
        // If impersonation is set, override user type
        if (isset($_SESSION['impersonate_type'])) {
            $user['type'] = $_SESSION['impersonate_type'];
        }
        return $user;
    }

    // Set layout based on portal and user type
    protected function setLayout() {
        // Choose the layout file depending on the portal.
        // You might also choose a different layout if the user is impersonating.
        if ($this->user && $this->user['type'] !== $this->portal) {
            // If impersonating a different portal, you could either show a warning
            // or render a hybrid layout. This is an example:
            $this->layout = __DIR__ . '/../Views/Layouts/impersonation.php';
        } else {
            $this->layout = __DIR__ . '/../Views/Layouts/' . $this->portal . '.php';
        }
    }

    // Render the view inside the layout.
    protected function render($view, $data = []) {
        // Make the $data available to the view.
        extract($data);
        // Buffer the view's output.
        ob_start();
        include __DIR__ . '/../Views/' . $this->portal . '/' . $view . '.php';
        $content = ob_get_clean();
        // Now include the layout, which will use $content.
        include $this->layout;
    }
}
