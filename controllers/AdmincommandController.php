<?php
class AdminCommandController extends Controller {
    public function index() {
        try {
            
            //initialisation des classes de requetes
            $commandeModel = new Commande();
            $objetCommandeModel = new Objet_Commande();
            $serieMangaModel = new Serie_Manga();
            $tomeModel = new Tome();
            $avisModel = new Avis();

            //si la session n'est pas active alors l'active
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            //si l'utilisateur n'est pas connecter, le redirige avec un message d'erreur
            if(!isset($_SESSION['login']) || !$_SESSION['login']){
                header("Location: login?notification=Vous n'êtes pas connecté&type=error");
                exit();
            }
            
            //si l'utilisateur n'est pas administrateur
            if($_SESSION['username'] != "admin" && $_SESSION['id'] != 1){
                header("Location: login?notification=Vous n'êtes pas administrateur&type=error");
                exit();
            }

            //recupere l'action et l'id de la commande
            $action = $_GET['action'] ?? null;
            $actionIdCommand = $_GET['id'] ?? null;

            //si l'action est cancel alors attribute le status "annulée" la commande
            if ($action === 'cancel' && $actionIdCommand) {
                if ($commandeModel->adminCancelCommande($actionIdCommand)) {
                    header('Location: admincommand?notification=Commande annulée avec succès&type=success');
                } else {
                    header('Location: admincommand?notification=Impossible d\'annuler la commande&type=error');
                }
                exit;
            }
            
            //si l'action est deliver alors attribute le status "livrée" la commande
            if ($action === 'deliver' && $actionIdCommand) {
                if ($commandeModel->adminDeliverCommande($actionIdCommand)) {
                    header('Location: admincommand?notification=Commande marquée comme livrée&type=success');
                } else {
                    header('Location: admincommand?notification=Impossible de marquer la commande comme livrée&type=error');
                }
                exit;
            }

            //si l'action est delete alors supprime la commande
            if ($action === 'delete' && $actionIdCommand) {
                if ($commandeModel->adminDeleteCommande($actionIdCommand)) {
                    header('Location: admincommand?notification=Commande supprimée de l\'historique&type=success');
                } else {
                    header('Location: admincommand?notification=Erreur : La commande doit être annulée avant d\'être supprimée&type=error');
                }
                exit;
            }

            //recupere toutes les commandes
            $allOrders = $commandeModel->getAllCommandesAdmin();
            $userOrders = [];
            
            //recupere les information des tomes
            foreach ($allOrders as $order) {
                $items = $objetCommandeModel->getByCommande($order['id']);
                $orderItems = [];
                
                foreach ($items as $item) {
                    $idTome = $item['id_tome'];
                    
                    $tomeInfos = $tomeModel->findById($idTome);
                    $numero_volume = $tomeInfos['numero_volume'] ?? '';

                    $serieInfo = $serieMangaModel->getAllByTomeId($idTome);
                    $titre = $serieInfo['titre'] ?? 'Titre inconnu';
                    $idSerie = $serieInfo['id_serie'] ?? '';
                    $image_tome = $serieInfo['chemin_image'] ?? '';
                    
                    $notes = $avisModel->getNoteByTome($idTome) ?? [];
                    $i = 0;
                    $totalnote = 0;
                    foreach($notes as $uneNote){
                        if (isset($uneNote['note'])) {
                            $i++;
                            $totalnote += (int)$uneNote['note'];
                        }
                    }
                    $note = ($i > 0) ? ($totalnote / $i) : 0;

                    $orderItems[] = [
                        'id_tome' => $idTome,
                        'id_serie' => $idSerie,
                        'titre' => $titre,
                        'image' => $image_tome,
                        'price' => $item['prix_achat'],
                        'quantite' => $item['quantite'],
                        'numero_volume' => $numero_volume,
                        'note' => $note,
                        'reviews' => $i
                    ];
                }
                
                $order['items'] = $orderItems;
                $userOrders[] = $order;
            }

            //recupere le message de notification et son type (si il y en a)
            $message = $_GET['notification'] ?? null;
            $typeMessage = $_GET['type'] ?? null;

            $this->view('admincommand', [
                'title' => "MILLE SABORDS - Administrateur", 
                'orders' => $userOrders, 
                'message' => $message, 
                'typeMessage' => $typeMessage
            ], false);

        } catch (Throwable $e) {
            http_response_code(500);
            echo "View Rendering Error: " . htmlspecialchars($e->getMessage());
        }
    }
}