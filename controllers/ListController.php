<?php
class ListController extends Controller {
    public function index() {
        try {
            $SM = new Serie_Manga();
            $TAG = new Tag();

            //prepare la notification
            $message = $_GET['notification'] ?? null;
            $typeMessage = $_GET['type'] ?? "error";

            /**
            * @param $SM = instance de la classe Serie_Manga()
            * @param string $tag = nom d'un tag
            * @param array $usedIds = liste des id de manga deja afficher
            * @return int l'id d'un manga aléatoire qui posséde le tag donné en parametre
            */
            /*function idFiltrer($SM, $tag, &$usedIds) : ?int {
                for ($i = 0; $i < 10; $i++) {
                    $id = $SM->getRandomIdByTagName($tag);

                    if ($id && $SM->getMangaImageUrl($id) 
                        && !in_array($id, $usedIds)) {

                        $usedIds[] = $id;
                        return $id;
                    }
                }
                return null;
            }*/
            
            /**
            * @param $tagModel = instance de la classe Tag()
            * @return int nom d'un tag aléatoire (exclu : ['Hentai', 'Ecchi', 'Erotica'])
            */
            function getNomTag($tagModel) : ?string {
                do {
                    $nom = $tagModel->getRandomTag($tagModel->getNsfwTags());
                } while ($nom !== null && !$tagModel->hasManga($nom));

                return $nom;
            }

            
            $list = [];
            $themes = [];

            //prend 4 themes aléatoire
            while (count($themes) < 4) {
                $newTag = getNomTag($TAG);
                if ($newTag !== null && !in_array($newTag, $themes)) {
                    $themes[] = $newTag;
                }
            }

            //prend des mangas aléatoire dans le theme choisi
            foreach ($themes as $theme) {
                $listBannedId = [361, 50, 491, 681, 892];
                $listOfId = $SM->GetListIdMangaByTag($theme, 50, $SM->getNsfwTags(), $listBannedId);
                // $listOfId = $SM->GetListIdMangaByTag($theme, 10);
                $allDataManga = [];
                foreach($listOfId as $id){
                    $allDataManga[] = $SM->getAllById($id);
                }

                $list[$theme] = $allDataManga;
            }
            $this->view('list', [ 'title' => "MILLE SABORDS - Bibliothèque", 'list' => $list, 'message' => $message, 'typeMessage' => $typeMessage]);

        } catch (Throwable $e) {
            http_response_code(500);
            echo "View Rendering Error: " . htmlspecialchars($e->getMessage());
        }
    }
}
