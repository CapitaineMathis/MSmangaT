<?php
class MangaController extends Controller {
    public function index() {
        try {
            //initialisation des classes de requetes
            $tomeModel = new Tome();
            $SM = new Serie_Manga();
            
            //recupere l'id du manga choisi
            $idSerie = $_GET['id'] ?? null;
            if(!$idSerie){
                header('Location: list');
                exit();
            }

            //prepare les informations
            $manga = $SM->getAllById($idSerie);
            $titre = $manga['titre'];
            $listTome = $tomeModel->findBySerie($idSerie);

            //redirection si pas de titre
            if (!$titre) {
                header("Location: list?notification=Cette oeuvre n'existe pas&type=error");
                exit();
            }

            //redirection si pas de tome
            if(!$listTome){
                header("Location: list?notification=Cette oeuvre n'a aucun tome&type=error");
                exit();
            }
            
            $this->view('manga', ['title' => "MILLE SABORDS - $titre ", 'tome' => $listTome, 'idSerie' => $idSerie]);
        } catch (Throwable $e) {
            http_response_code(500);
            echo "View Rendering Error: " . htmlspecialchars($e->getMessage());
        }
    }
}
