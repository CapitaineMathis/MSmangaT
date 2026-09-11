<?php
class CommandTrackerController extends Controller {
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

            //si pas connecter redirige vers la page de connection
            if (!isset($_SESSION['id'])) {
                header('Location: ./login?origine=commandtracker');
                exit;
            }

            
            //initialise les actions
            $action = $_GET['action'] ?? null;
            $actionIdCommand = $_GET['id'] ?? null;

            //si l'action est cancel alors attribute le status "annulée" la commande
            if ($action === 'cancel' && $actionIdCommand) {
                if ($commandeModel->cancelCommande($actionIdCommand, $_SESSION['id'])) {
                    header('Location: commandtracker?notification=Commande annulée avec succès&type=success');
                } else {
                    header('Location: commandtracker?notification=Impossible d\'annuler la commande&type=error');
                }
                exit;
            }

            //si l'action est delete alors supprime la commande
            if ($action === 'delete' && $actionIdCommand) {
                if ($commandeModel->deleteCommande($actionIdCommand, $_SESSION['id'])) {
                    header('Location: commandtracker?notification=Commande supprimée de votre historique&type=success');
                } else {
                    header('Location: commandtracker?notification=Erreur : La commande doit être annulée avant d\'être supprimée&type=error');
                }
                exit;
            }

            //recupere les commandes du client
            $allOrders = $commandeModel->all($_SESSION['id']);
            $userOrders = [];
            
            //recupere les informations des tomes
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
                    //recupere la note
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

            //gere les notification
            $message = $_GET['notification'] ?? null;
            $typeMessage = $_GET['type'] ?? null;

            $this->view('commandtracker', [
                'title' => "MILLE SABORDS - Vos Commandes", 
                'orders' => $userOrders,
                'message' => $message,
                'typeMessage' => $typeMessage
            ]);
        } catch (Throwable $e) {
            http_response_code(500);
            echo "View Rendering Error: " . htmlspecialchars($e->getMessage());
        }
    }
}