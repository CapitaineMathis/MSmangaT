<?php
class LoginController extends Controller {
    public function index() {
        try {
            //initialise la classe pour les requetes
            $User = new Utilisateur();
            
            //prepare l'origine pour la redirection finale
            $redirect = $_GET['origine'] ?? 'list';

            //prepare la notification
            $message = $_GET['notification'] ?? null;
            $typeMessage = $_GET['type'] ?? "error";

            //si l'utilisateur a envoyer des information les verifie
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $username = trim($_POST['identifiant'] ?? '');
                $password = $_POST['password'] ?? '';
                
                if ($username && $password) {
                    $userInfo = $User->findByUsername($username);

                    //verifie les informations si vrai connecte l'utilisateur
                    if ($userInfo && password_verify($password, $userInfo['password'])) {
                        if (session_status() === PHP_SESSION_NONE) {
                            session_start();
                        }
                        session_regenerate_id(true);

                        $_SESSION['id'] = $userInfo['id'];
                        $_SESSION['username'] = $userInfo['username'];
                        $_SESSION['login'] = true;
                        
                        header('Location: ./'. $redirect .'?notification=Vous êtes connecter en tant que '. $username.'&type=success');
                        exit();
                    }
                }
                $message = "Erreur : identifiant ou mot de passe incorrect";
            }

            $this->view('login', ['title' => "MILLE SABORDS - Se connecter", 'redirect' => $redirect, 'message' => $message, 'typeMessage' => $typeMessage], false);
            
        } catch (Throwable $e) {
            http_response_code(500);
            echo "View Rendering Error: " . htmlspecialchars($e->getMessage());
        }
    }
}