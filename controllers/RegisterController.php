<?php
class RegisterController extends Controller {
    public function index() {
        try {
            //initialise la classe pour les requetes
            $User = new Utilisateur();
            
            //prepare l'origine pour la redirection finale
            $redirect = $_GET['redirect'] ?? 'list';

            //prepare la notification
            $message = $_GET['notification'] ?? null;
            $typeMessage = $_GET['type'] ?? "error";

            //si l'utilisateur a envoyer des information les verifie
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $username = trim($_POST['identifiant'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $password = $_POST['password'] ?? null;
                
                //verifie si tout les champs sont entrer
                if ($username && $email && $password) {
                    $isUserExist = $User->findByEmail($email);
                    $isUsernameExist = $User->findByUsername($username);

                    //si l'utilisateur existe deja / deja utiliser
                    if ($isUserExist) {
                        $message = "Email déjà utilisé";
                    } elseif ($isUsernameExist) {
                        $message = "Identifiant déjà utilisé";
                    } else {

                        //crée le compte
                        $passwordHashed = password_hash($password, PASSWORD_DEFAULT); 
                        $data = [$username, $email, $passwordHashed];
                        $User->createWithoutTel($data);
                        
                        $userInfo = $User->findByEmail($email);

                        //si tout est bon alors le connecte
                        if ($userInfo && isset($userInfo['id'])) {
                            if (session_status() === PHP_SESSION_NONE) {
                                session_start();
                            }
                            session_regenerate_id(true);
                            
                            $_SESSION['id'] = $userInfo['id'];
                            $_SESSION['username'] = $userInfo['username'];
                            $_SESSION['login'] = true;
                            
                            header('Location: ./'. $redirect .'?notification=Compte crée avec succès');
                            exit();
                        } else {
                            $message = "Erreur lors de la récupération de l'utilisateur après création.";
                        }
                    }
                } else {
                    $message = "Veuillez remplir tous les champs.";
                }
            }

            $this->view('register', ['title' => "MILLE SABORDS - S'Inscrire", 'message' => $message, 'typeMessage' => $typeMessage, 'redirect' => $redirect], false);
            
        } catch (Throwable $e) {
            http_response_code(500);
            echo "View Rendering Error: " . htmlspecialchars($e->getMessage());
        }
    }
}