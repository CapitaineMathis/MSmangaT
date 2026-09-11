<?php
class CommandController extends Controller {
    public function index() {
        try {
            $config_file = './config.local.php';
            if (!file_exists($config_file)){
                die("Erreur: the config file dose not exist");
            }
            $config = require $config_file;
            
            //initialisation des classes de requetes
            $P = new Panier();
            $OP = new Objet_Panier();
            $User = new Utilisateur();
            $commandeModel = new Commande();
            $objetCommandeModel = new Objet_Commande();
            $tomeModel = new Tome();
            $SM = new Serie_Manga();

            //si la session n'est pas active alors l'active
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            //initialisation du client stripe
            require_once __DIR__ . '/../.stripe-php-master/init.php';
            $stripe = new \Stripe\StripeClient($config['client-stripe']);
    
            $contenuePanier = [];

            //si l'utilisateur est connecter (True ou False)
            $isLogged = isset($_SESSION['login']) && $_SESSION['login'];
            
            //recupere les parametre d'url
            $message = $_GET['notification'] ?? null;
            $typeMessage = $_GET['type'] ?? null;
            $action = $_GET['action'] ?? null;
            $idTome = $_GET['id'] ?? null;
            $sessionId = $_GET['session_id'] ?? null;

            //recupere le chemin 
            $baseUrl = strtok($_SERVER["REQUEST_URI"], '?');

            if ($isLogged) {

                $idUser = $_SESSION['id'];
                
                //verifie si il y a une session stripe
                if ($sessionId) {
                    try {
                        $session = $stripe->checkout->sessions->retrieve($sessionId);
                        
                        //demande a stripe si le client a payer 
                        // si oui valide la commande 
                        // si non affiche une erreur
                        if ($session->payment_status === 'paid') {
                                $order_id = $session->metadata->order_id;

                                $commandeModel->updateStatut($order_id, 'En attente de livraison', 'En attente de paiement');

                                $tomeModel->reduceStockByCommande($order_id);

                                $P->clearPanier($idUser);
                                header("Location: commandtracker?notification=Paiement validé, merci pour votre commande&type=success");
                                exit();
                            } else {
                                header("Location: command?notification=Erreur : Erreur lors du paiement&type=error");
                                exit();
                            }


                    } catch (\Exception $e) {
                        header("Location: pannier?notification=Erreur : utilisation de Stripe : " . $e->getMessage()."&type=error");
                        exit();
                    }
                    
                }
                
                //si l'utilisateur ajoute/retire/supprime un tome de sa commande
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

                //verifie si l'utilisateur a un panier
                $contenuePanier = $OP->getPanierByUserId($idUser);
                if (empty($contenuePanier)) {
                    header("Location: pannier?notification=Erreur : Votre panier est vide.&type=error");
                    exit();
                }


                //calcule du prix total
                $id_commande = $commandeModel->userCommandeNotPaid($idUser);
                $prix_total = 0;

                foreach ($contenuePanier as $tome) {
                    $prix_total += $tome['price'] * $tome['quantite'];
                }

                //cree la commande si elle n'existe pas
                if (!$id_commande){
                    $id_commande = $commandeModel->create(
                        $prix_total, 
                        $idUser, 
                        'En attente de paiement'
                    );
                } else {
                    $commandeModel->updateMontantTotal($id_commande, $prix_total);
                    $objetCommandeModel->clearByCommande($id_commande);
                }

                //recupere les information des tomes commandés
                foreach ($contenuePanier as $tome) {
                    $idTomeObj = $tome['id_tome'] ?? $tome['id'] ?? $tome['idTome'];

                    $objetCommandeModel->addObjectToCommande(
                        $tome['quantite'], 
                        $tome['price'],
                        $idTomeObj,
                        $id_commande
                    );
                }

                //met les informations du client dans la bdd
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $User->addCommandInfo(
                        $_POST['nom'], 
                        $_POST['prenom'], 
                        $_POST['telephone'], 
                        $_POST['numero_rue'], 
                        $_POST['nom_rue'], 
                        $_POST['ville'], 
                        $_POST['code_postal'], 
                        $idUser,
                        $_POST['complement'] ?? null
                    );
                    

                    //prepare le paiement avec stripe
                    try {
                        header('Content-Type: application/json');

                        $line_items = [];
                        foreach ($contenuePanier as $tome) {
                            
                            //prepare les information des tome
                            $idTome = $tome['id'] ?? $tome['idTome'] ?? $tome['id_tome'];
                            $serieInfo = $SM->getAllByTomeId($idTome);
                            $serieTitre = $serieInfo['titre'] ?? 'Titre inconnu';
                            $titre = $serieTitre ." - Tome ". $tome['numero_volume'];

                            //met les information dans la commande stripe
                            $line_items[] = [
                                'price_data' => [
                                    'currency' => 'eur',//devise de monaie
                                    'product_data' => [
                                        'name' => $titre,
                                    ],
                                    'unit_amount' => $tome['price'] * 100, //centimes
                                ],
                                'quantity' => $tome['quantite'],
                            ];
                        }

                        //cree une session stripe avec les informations
                        $checkout_session = $stripe->checkout->sessions->create([
                            'line_items' => $line_items,
                            'mode' => 'payment',
                            'client_reference_id' => $id_commande, 
                            'metadata' => [
                                'order_id' => $id_commande 
                            ],
                            //notification en cas de reussite ou erreur
                            'success_url' => 'https://'.$_SERVER['HTTP_HOST'].'/command?session_id={CHECKOUT_SESSION_ID}',
                            'cancel_url' => 'https://'.$_SERVER['HTTP_HOST'].'/pannier?notification=Erreur : Erreur lors du paiement&type=error',
                        ]);

                        //redirige vers la page de paiement
                        header("HTTP/1.1 303 See Other");
                        header("Location: " . $checkout_session->url);
                        exit();

                    } catch (\Exception $e) {
                        header("Location: pannier?notification=Erreur : utilisation de Stripe : " . $e->getMessage()."&type=error");
                        exit();
                    }
                }

            }
            //redirige vers la page de login si pas connecter
            else {
                header("Location: login");
                exit();
            }

            $this->view('command', ['title' => "MILLE SABORDS - Votre Commande", 'contenuePanier' => $contenuePanier, 'isLogged' => $isLogged, 'message' => $message, 'typeMessage' => $typeMessage]);
        } catch (Throwable $e) {
            http_response_code(500);
            echo "View Rendering Error: " . htmlspecialchars($e->getMessage());
        }
    }
}


