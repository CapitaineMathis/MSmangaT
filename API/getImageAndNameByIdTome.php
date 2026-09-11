<?php
    $projectRoot = dirname(__DIR__); 

    $dbPath = $projectRoot . '/core/Database.php';
    $modelPath = $projectRoot . '/core/Model.php';
    $seriePath = $projectRoot . '/models/Tome.php';

    if (file_exists($dbPath)) {
        require_once $dbPath;
    }

    require_once $modelPath;
    require_once $seriePath;

    ob_clean(); 

    header('Content-Type: application/json');

    try {
        $tomeModel = new Tome();
        $idTome = $_GET['id'] ?? '';
        
        $infoTome = $tomeModel->getTomeImageUrlAndSerieTitleById($idTome);

        echo json_encode([
            'success' => true,
            'data' => $infoTome
        ]);
        exit;

    } catch (Throwable $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
        exit;
    }