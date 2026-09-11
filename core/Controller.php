<?php

class Controller {

    public function __construct() {
        //active la session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        
    }

    protected function view($view, $data = [], $showFooter = true)
    {
        extract($data, EXTR_SKIP);

        $head = __DIR__ . '/../views/layouts/head.php';
        $header = __DIR__ . '/../views/layouts/header.php';
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        $footer = __DIR__ . '/../views/layouts/footer.php';
        
        //gestion des erreurs
        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo "Error: View '$view' not found.";
            return;
        }

        require $head;
        require $header;
        require $viewFile;

        //permet de mettre ou non le footer
        if ($showFooter) {
            require $footer;
        }

        //gere les notifications
        if (isset($_GET['notification'])) {
            $message = $_GET['notification'] ?? ""; 
            $typeMessage = $_GET['type'] ?? "error";
            
            echo '<div id="system-message" data-message="' . htmlspecialchars($message) . '"></div>';
            echo '<div id="type-message" data-message="' . htmlspecialchars($typeMessage) . '"></div>';
        }
    }

}