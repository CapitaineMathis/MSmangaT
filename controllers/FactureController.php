<?php
class FactureController extends Controller {
    public function index() {
        try {
            //initialisation des classes de requetes
            $Commande = new Commande();
            $objetCommandeModel = new Objet_Commande();
            $serieMangaModel = new Serie_Manga();
            $tomeModel = new Tome();
            $utilisateurModel = new Utilisateur();

            //si la session n'est pas active alors l'active
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            //recupere l'action et l'id de la commande
            $action = $_GET['action'] ?? null;
            $actionIdCommand = $_GET['id'] ?? null;

            //si l'utilisateur n'est pas connecter le redirige
            if(!$_SESSION['id']){
                header("Location: login");
                exit();
            }


            $UserCommand = $Commande->getCommandById($actionIdCommand);

            $ifUserCommand = $Commande->userHaveCommande($_SESSION['id'], $actionIdCommand);
            $isAdmin = ($_SESSION['id'] == 1 || $_SESSION['username'] == "admin");

            //verifie si l'utilisateur possede la commande si non le redirige
            if (!$ifUserCommand && !$isAdmin) {
                header("Location: commandtracker?notification=Erreur : Cette commande n'est pas la votre&type=error");
                exit();
            }

            //verifie le status d'une commande
            $statut = $UserCommand['statut'] ?? '';
            if ($statut !== "En attente de livraison" && $statut !== "Livrée") {
                header("Location: commandtracker?notification=Erreur : Cette commande n'a pas encore de facture disponible&type=error");
                exit();
            }

            if ($action === 'facture' && $actionIdCommand) {
                require_once '../MSmangaT/.dompdf/autoload.inc.php';

                $user = $utilisateurModel->findById($_SESSION['id']);

                $items = $objetCommandeModel->getByCommande($actionIdCommand);
                $orderItems = [];
                $sousTotalHT = 0;

                //prepare les informations de la commande
                foreach ($items as $item) {
                    $tomeInfos = $tomeModel->findById($item['id_tome']);
                    $serieInfo = $serieMangaModel->getAllByTomeId($item['id_tome']);

                    $titre = $serieInfo['titre'] ?? 'Titre inconnu';
                    $volume = $tomeInfos['numero_volume'] ?? '';
                    $prixTTC = $item['prix_achat'];
                    $prixHT = $prixTTC / 1.055;
                    $quantite = $item['quantite'];
                    $montantLigneTTC = $prixTTC * $quantite;
                    
                    $sousTotalHT += ($prixHT * $quantite);

                    $orderItems[] = [
                        'description' => $titre . " - Tome " . $volume,
                        'quantite' => $quantite,
                        'unite' => 'pièce',
                        'prix_unitaire_ht' => $prixHT,
                        'montant_ttc' => $montantLigneTTC
                    ];
                }

                $tva = $sousTotalHT * 0.055;
                $totalTTC = $sousTotalHT + $tva;
                $dateFacture = date('d/m/Y');
                ob_start();
                include 'views/facture.php';

                //prepare le pdf
                $html = ob_get_clean(); 
                $dompdf = new \Dompdf\Dompdf();
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait'); 
                $dompdf->render();

                // stream() envoie le PDF au navigateur. 
                $dompdf->stream("Facture_MilleSabords_N" . $actionIdCommand . ".pdf", ["Attachment" => 1]);
                exit;
            }
            else {
                header("Location: commandtracker?notification=Erreur : Erreur lors du téléchargement de la facture&type=error");
                exit();
            }
            $this->view('facture', ['title' => "MILLE SABORDS - Votre facture", 'user' => $user, 'orderItems' => $orderItems, 'sousTotalHT'=> $sousTotalHT]);
        } catch (Throwable $e) {
            http_response_code(500);
            echo "View Rendering Error: " . htmlspecialchars($e->getMessage());
        }
    }
}
