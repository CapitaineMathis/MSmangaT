<?php
class LogoutController extends Controller {
    public function index() {
        try {
            //si la session n'est pas active alors l'active
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            //si l'utilisateur n'est pas connecter, le redirige avec un message d'erreur
            if(!$_SESSION['login']){
                header("Location: login?notification=Vous n'êtes pas connecté&type=error");
                exit();
            }

            //sinon deconnecte l'utilisateur
            else{
                $_SESSION['login'] = false;
                $_SESSION['id'] = null;
                $_SESSION['username'] = null;
                header("Location: login?notification=Vous n'êtes plus connecté&type=success");
                exit();
            }

            $this->view('logout', ['title' => "MILLE SABORDS"]);
        } catch (Throwable $e) {
            http_response_code(500);
            echo "View Rendering Error: " . htmlspecialchars($e->getMessage());
        }
    }
}
