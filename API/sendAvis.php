<?php
    $projectRoot = dirname(__DIR__); 

    $dbPath = $projectRoot . '/core/Database.php';
    $modelPath = $projectRoot . '/core/Model.php';
    $seriePath = $projectRoot . '/models/Avis.php';

    if (file_exists($dbPath)) {
        require_once $dbPath;
    }

    require_once $modelPath;
    require_once $seriePath;

    ob_clean(); 

    header('Content-Type: application/json');

    try {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $avisModel = new Avis();

        $idUser = $_SESSION['id'] ?? null;

        $idTome = $_POST['id'] ?? null;
        $commentaire = $_POST['commentaire'] ?? '';
        $note = $_POST['note'] ?? 0;
        
        if ($idTome && $idUser){
            $success = $avisModel->addAvis($idUser, $idTome, $commentaire, $note);
        }
        

        echo json_encode([
            'success' => true,
            'data' => $success
        ]);
        exit;

    } catch (Throwable $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
        exit;
    }