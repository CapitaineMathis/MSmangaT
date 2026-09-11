<?php
class HomeController extends Controller {
    public function index() {
        try {
            $SM = new Serie_Manga();

            $list="";
            $this->view('home', ['title' => 'MILLE SABORDS - Accueil','list'=>$list]);
        } catch (Throwable $e) {
            http_response_code(500);
            echo "View Rendering Error: " . htmlspecialchars($e->getMessage());
        }
    }
}
