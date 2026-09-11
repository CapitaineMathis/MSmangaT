<?php
    $projectRoot = dirname(__DIR__); 

    $dbPath = $projectRoot . '/core/Database.php';
    $modelPath = $projectRoot . '/core/Model.php';
    $seriePath = $projectRoot . '/models/Serie_Manga.php';

    if (file_exists($dbPath)) {
        require_once $dbPath;
    }

    require_once $modelPath;
    require_once $seriePath;

    ob_clean(); 

    header('Content-Type: application/json');

    try {
        $mangaModel = new Serie_Manga();
        $id = $_GET['id'] ?? '';
        
        $infoManga = $mangaModel->getMangaImageUrlAndtitleById($id);

        echo json_encode([
            'success' => true,
            'data' => $infoManga
        ]);
        exit;

    } catch (Throwable $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
        exit;
    }
