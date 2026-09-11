<?php

class Serie_Manga extends Model {

    protected $table = "Serie_Manga";

    /**
    * Recherche des séries/mangas correspondant au texte fourni.
    *
    * @param string $text Le texte à rechercher.
    * @return array Un tableau d'entiers contenant les identifiants des séries/mangas correspondants.
    */
    public function research(string $text) : array {
        $stmt = $this->db->prepare("SELECT id, titre, title_japanese 
            FROM $this->table 
            WHERE MATCH(titre, title_english, title_japanese) 
            AGAINST(:recherche IN NATURAL LANGUAGE MODE)");

        $stmt->execute(['recherche' => $text]);

        $series = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $list = [];
        foreach($series as $serie){
            $list[] = $serie["id"];
        }
        return $list;
    }

    public function getMangaImageUrl(int $id) {
        $stmt = $this->db->prepare("SELECT chemin_image FROM $this->table WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? $result['chemin_image'] : null;
    }
    
    /**
    * :D
    * @param array $BanTag liste des tag a suppr
    * @param array $banId liste les ID a suppr
    * @return int l'id d'un manga aléatoire
    */
    public function getRandomIdWithBannedTag(array $BanTag = [], array $banId = []) : int {
        $sql = "SELECT Serie_Manga.id FROM $this->table";
        $whereConditions = [];

        if (!empty($banId)) {
            $whereConditions[] = "Serie_Manga.id NOT IN (" . implode(", ", array_map('intval', $banId)) . ")";
        }

        if (count($BanTag) != 0) {
            $sql .= " JOIN CARACTERISER ON CARACTERISER.id_serie = Serie_Manga.id";
            $sql .= " JOIN Tag ON Tag.id = CARACTERISER.id_tag";
            $whereConditions[] = "Tag.nom_tag NOT IN ('" . implode("', '", array_map('htmlspecialchars', $BanTag)) . "')";
        }

        if (!empty($whereConditions)) {
            $sql .= " WHERE " . implode(" AND ", $whereConditions);
        }

        $sql .= " ORDER BY RAND() LIMIT 1;";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? (int)$result['id'] : null;
    }

    /**
    * @param array $Tag = liste des tags dont on veux recup les id
    * @return int l'id d'un manga aléatoire avec le tag
    */
    public function getRandomIdWithTag(array $Tag = []) : int {
        $sql = "SELECT Serie_Manga.id FROM $this->table";
        $whereConditions = [];

        if (count($Tag) != 0) {
            $sql .= " JOIN CARACTERISER ON CARACTERISER.id_serie = Serie_Manga.id";
            $sql .= " JOIN Tag ON Tag.id = CARACTERISER.id_tag";
            $whereConditions[] = "Tag.nom_tag IN ('" . implode("', '", array_map('htmlspecialchars', $Tag)) . "')";
        }

        if (!empty($whereConditions)) {
            $sql .= " WHERE " . implode(" AND ", $whereConditions);
        }

        $sql .= " ORDER BY RAND() LIMIT 1;";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? (int)$result['id'] : null;
    }

    /**
    * @param string tag dont on veux recup les id
    * @return int l'id d'un manga aléatoire avec le tag
    */
    public function getRandomIdByTagName(string $Tag) : ?int {
        $sql = "SELECT DISTINCT Serie_Manga.id FROM $this->table 
        JOIN CARACTERISER ON CARACTERISER.id_serie = Serie_Manga.id 
        JOIN Tag ON Tag.id = CARACTERISER.id_tag 
        WHERE nom_tag like :tag ORDER BY RAND() LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['tag' => $Tag]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? (int)$result['id'] : null;
    }

    /**
    * @param string name = nom du manga
    * @return int id du manga
    */
    public function getIdByName(string $name) : ?int {
        $sql = "SELECT id FROM $this->table WHERE titre LIKE :titre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'titre' => "%" . trim($name) . "%"
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? (int)$result['id'] : null;
    }

    /**
    * @param int id = id du manga
    * @return array tout les details d'un manga
    */
    public function getAllById(int $id){
        $sql = "SELECT * FROM $this->table WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $id
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }


    /**
    * Obtien une liste les manga qui on le même tag
    * @param string $Tag Nom du tag des manga a return.
    * @param array $ArraySize taille de la liste de manga.
    * @param array $BannedTag Liste des tag a ne pas return !
    * @param array $BannedId Liste des id de manga a ne pas return !
    * @return array une liste d'id de manga.
    */
    public function GetListIdMangaByTag(string $Tag = "", int $ArraySize = 1, array $BannedTag = [], array $BannedId = []) : array {
        $sql = "SELECT Serie_Manga.id FROM $this->table";
        $whereConditions = [];

        if (!empty($BannedTag) || $Tag != ""){
            $sql .= " JOIN CARACTERISER ON CARACTERISER.id_serie = Serie_Manga.id";
            $sql .= " JOIN Tag ON Tag.id = CARACTERISER.id_tag";
        }

        if (!empty($BannedId)) {
            $whereConditions[] = "Serie_Manga.id NOT IN (" . implode(", ", array_map('intval', $BannedId)) . ")";
        }

        if (!empty($BannedTag)) {
            $whereConditions[] = "Tag.nom_tag NOT IN ('" . implode("', '", array_map('htmlspecialchars', $BannedTag)) . "')";
        }

        if ($Tag != "") {
            $whereConditions[] = "Tag.nom_tag like :tag";
        }

        if (!empty($whereConditions)) {
            $sql .= " WHERE " . implode(" AND ", $whereConditions);
        }

        $sql .= " ORDER BY RAND() LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$ArraySize, PDO::PARAM_INT);
        if ($Tag != "") {
            $stmt->bindValue(':tag', "%$Tag%", PDO::PARAM_STR);
        }
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $list = [];
        foreach ($result as $row) {
            $list[] = (int)$row['id'];
        }
        
        return $list;
    }

    public function getNsfwTags() : array {
        return ['Hentai', 'Ecchi'];
    }

    /**
    * @param int idTome = id d'un tome d'un manga
    * @return array tout les details d'un manga
    */
    public function getAllByTomeId(int $idTome){
        $sql = "SELECT * FROM $this->table JOIN Tome ON Tome.id_serie = Serie_Manga.id WHERE Tome.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $idTome
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

    public function getMangaImageUrlAndtitleById(int $id) {
        $stmt = $this->db->prepare("SELECT chemin_image, titre FROM $this->table WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

}

