<?php
// Enable error reporting for development (disable in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Autoload core, controllers, and models
spl_autoload_register(function ($class) {
    foreach (['core','models', 'controllers'] as $dir) {
        $file = __DIR__ . "/$dir/$class.php";
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
    // If class file not found
    http_response_code(500);
    echo "Critical Error: Class '$class' could not be loaded.";
    exit;
});

try {
    $router = new Router();
    $router->dispatch($_GET['url'] ?? '');
} catch (Throwable $e) {
    http_response_code(500);
    echo "Application Error: " . htmlspecialchars($e->getMessage());
}
