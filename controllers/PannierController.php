<?php
class PannierController extends Controller {
    public function index() {
        try {
            //si la session n'est pas active alors l'active
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            //initialisation des classes de requetes
            $OP = new Objet_Panier();
            $P = new Panier();
            $tomeModel = new Tome();

            //recupere le tome ajouter si il y en a un
            $idTome = $_GET['id'] ?? null;


            $contenuePanier = [];

            //si connecter
            $isLogged = isset($_SESSION['login']) && $_SESSION['login'];
            

            //recupere les parametres d'url
            $action = $_GET['action'] ?? null;
            $message = $_GET['notification'] ?? null;
            $typeMessage = $_GET['type'] ?? null;

            //recupere le chemin 
            $baseUrl = strtok($_SERVER["REQUEST_URI"], '?');

            if ($action === "clear") {
                if ($isLogged) {
                    $idUser = $_SESSION['id'];
                    $P->clearPanier($idUser);
                } else {
                    setcookie('panier', '', [
                        'expires' => time() - 3600,
                        'path' => '/',
                        'domain' => ($_SERVER['HTTP_HOST'] !== 'localhost') ? $_SERVER['HTTP_HOST'] : false
                    ]);
                }
                header("Location: " . $baseUrl);
                exit();
            }

            if ($isLogged) {
                $idUser = $_SESSION['id'];
                $panier = $P->getByUser($idUser);

                if (!$panier) {
                    $P->create($idUser);
                }

                if (isset($_COOKIE['panier'])) {
                    $panierCookie = json_decode($_COOKIE['panier'], true);

                    if (is_array($panierCookie)) {
                        foreach ($panierCookie as $item) {
                            for ($i = 0; $i < $item['quantite']; $i++) {
                                $OP->addTomeAtPanier($item['idTome'], $idUser);
                            }
                        }
                    }

                    setcookie('panier', '', [
                        'expires' => time() - 3600,
                        'path' => '/',
                        'domain' => ($_SERVER['HTTP_HOST'] !== 'localhost') ? $_SERVER['HTTP_HOST'] : false
                    ]);
                    unset($_COOKIE['panier']);
                }

                if ($idTome) {
                    if ($action === "add" || !$action) {
                        $OP->addTomeAtPanier($idTome, $idUser);
                    } elseif ($action === "remove") {
                        $OP->removeTomeFromPanier($idTome, $idUser);
                    } elseif ($action === "delete") {
                        $OP->updateQuantiteTome($idTome, $idUser, 0); 
                    }
                    
                    header("Location: " . $baseUrl); 
                    exit();
                }

                $contenuePanier = $OP->getPanierByUserId($idUser);

            } else {
                $panier = isset($_COOKIE['panier']) ? json_decode($_COOKIE['panier'], true) : [];
                
                if ($idTome) {
                    $itemExists = false;
                    foreach ($panier as &$item) {
                        if ($item['idTome'] == $idTome) {
                            if ($action === "add" || !$action) $item['quantite']++;
                            if ($action === "remove") $item['quantite']--;
                            $itemExists = true;
                            break;
                        }
                    }

                    if (!$itemExists && ($action === "add" || !$action)) {
                        $panier[] = ['idTome' => $idTome, 'quantite' => 1];
                    }

                    $panier = array_filter($panier, function($item) use ($action, $idTome) {
                        if ($action === "delete" && $item['idTome'] == $idTome) return false;
                        return $item['quantite'] > 0;
                    });

                    setcookie('panier', json_encode(array_values($panier)), [
                        'expires' => time() + (86400 * 30),
                        'path' => '/',
                        'domain' => ($_SERVER['HTTP_HOST'] !== 'localhost') ? $_SERVER['HTTP_HOST'] : false,
                        'secure' => isset($_SERVER['HTTPS']),
                        'httponly' => false,
                        'samesite' => 'Lax'
                    ]);    
                    
                    header("Location: " . $baseUrl);
                    exit();
                }

                
                foreach ($panier as $item) {
                    $tomeInfos = $tomeModel->findById($item['idTome']);
                    if ($tomeInfos) {
                        $tomeInfos['quantite'] = $item['quantite'];
                        $contenuePanier[] = $tomeInfos;
                    }
                }
            }

            $this->view('panier', [ 'title' => "MILLE SABORDS - Votre Panier", 'contenuePanier' => $contenuePanier, 'isLogged' => $isLogged, 'message' => $message, 'typeMessage' => $typeMessage]);

        } catch (Throwable $e) {
            http_response_code(500);
            echo "Erreur : " . htmlspecialchars($e->getMessage());
        }
    }
}