<?php

class AdminMangaController extends Controller
{
    public function index()
    {
        try {
            if (session_status() === PHP_SESSION_NONE) session_start();

            // Sécurité
            if (!isset($_SESSION['login']) || !$_SESSION['login']) {
                header("Location: login?notification=Vous n'êtes pas connecté&type=error");
                exit();
            }

            if ($_SESSION['username'] !== "admin" && $_SESSION['id'] != 1) {
                header("Location: login?notification=Vous n'êtes pas administrateur&type=error");
                exit();
            }

            // Traitement POST
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                header('Content-Type: application/json; charset=utf-8');

                $action = $_POST['action'] ?? null;

                if (!$action) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Action manquante']);
                    return;
                }

                switch ($action) {
                    case 'addOeuvre':
                        echo json_encode($this->handleAddOeuvre());
                        return;

                    case 'addTome':
                        echo json_encode($this->handleAddTome());
                        return;

                    default:
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => 'Action inconnue']);
                        return;
                }
            }

            // Affichage
            $this->view('adminmanga', [
                'title' => "Admin Manga"
            ], false);

        } catch (Throwable $e) {
            http_response_code(500);
            echo "Erreur : " . htmlspecialchars($e->getMessage());
        }
    }

    // IMAGE UPLOAD OU URL
    private function saveUploadedImageOrUseUrl($file, $url)
    {
        // 1. Priorité au fichier uploadé
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            $tmpName = $file['tmp_name'];
            
            if (is_uploaded_file($tmpName)) {
                $mime = mime_content_type($tmpName);
                
                if (in_array($mime, $allowed)) {
                    // Utilisation de $_SERVER['DOCUMENT_ROOT'] pour pointer correctement vers le dossier de destination
                    // Ex: /var/www/html/AP/MSmangaT/public/img/
                    $targetDir = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/AP/MSmangaT/public/img/';

                    if (!is_dir($targetDir)) {
                        mkdir($targetDir, 0755, true);
                    }

                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $name = bin2hex(random_bytes(8)) . '.' . $ext;
                    $targetPath = $targetDir . $name;

                    if (move_uploaded_file($tmpName, $targetPath)) {
                        // On retourne le chemin URL relatif pour que le frontend le trouve
                        return '/AP/MSmangaT/public/img/' . $name; 
                    }
                }
            }
        }

        // 2. Si pas de fichier, on utilise le lien URL
        if (!empty($url) && filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }

        return null;
    }

    // OEUVRE 
    private function handleAddOeuvre()
    {
        $titre = trim($_POST['titre_oeuvre'] ?? $_POST['titre'] ?? '');
        $synopsis = trim($_POST['synopsis'] ?? '');
        $date_debut = $_POST['date_debut'] ?? $_POST['date_publication'] ?? null;
        $date_fin = $_POST['date_fin'] ?? null;
        
        $imageFile = $_FILES['image_oeuvre'] ?? null;
        $imageUrl = $_POST['image_url_oeuvre'] ?? null;

        if (!$titre) {
            return ['success' => false, 'message' => 'Titre obligatoire'];
        }

        $imagePath = $this->saveUploadedImageOrUseUrl($imageFile, $imageUrl);

        $data = [
            'titre' => $titre,
            'synopsis' => $synopsis,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'chemin_image' => $imagePath
        ];
        
        $m = new Serie_Manga();
        $m->insert($data);

        return ['success' => true, 'message' => 'Oeuvre ajoutée avec succès !'];
    }

    // TOME
    private function handleAddTome()
    {
        $oeuvre_id = $_POST['oeuvre_id'] ?? $_POST['serie_id'] ?? null;
        $numero = $_POST['numero_tome'] ?? $_POST['numero'] ?? null;
        $quantite = isset($_POST['quantite']) ? (int)$_POST['quantite'] : 0;
        $date_publication = $_POST['date_publication'] ?? $_POST['date_debut'] ?? null;
        
        $imageFile = $_FILES['image_tome'] ?? null;
        $imageUrl = $_POST['image_url_tome'] ?? null;

        if (!$oeuvre_id || !$numero) {
            return ['success' => false, 'message' => 'Champs obligatoires manquants (oeuvre_id, numero)'];
        }

        $imagePath = $this->saveUploadedImageOrUseUrl($imageFile, $imageUrl);

        $data = [
            'id_serie' => $oeuvre_id,
            'numero_volume' => $numero,
            'quantite_stock' => $quantite,
            'date_publication' => $date_publication,
            'chemin_image' => $imagePath,
            'id_type_edition' => 1,
            'id_maison' => 1
        ];

        $m = new Tome();
        $m->insert($data);

        return ['success' => true, 'message' => 'Tome ajouté avec succès !'];
    }
}