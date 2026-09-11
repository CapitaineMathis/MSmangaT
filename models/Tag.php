<?php

class Tag extends Model {

    protected $table = "Tag";

    public function findAll() {
        return $this->db->query("SELECT * FROM $this->table")->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 
     */
    public function getTagNameById($id) {
        $stmt = $this->db->prepare("SELECT nom_tag FROM $this->table WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? $result['nom_tag'] : null;
    }

    public function getRandomTag(array $BanTag = []) : ?string {
        $sql = "SELECT nom_tag FROM Tag";
        
        if (!empty($BanTag)) {
            $placeholders = implode(',', array_fill(0, count($BanTag), '?'));
            $sql .= " WHERE nom_tag NOT IN ($placeholders)";
        }

        $sql .= " ORDER BY RAND() LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($BanTag);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? $result['nom_tag'] : null;
    }

    public function hasManga(string $tag): bool {
        $sql = "SELECT COUNT(*) as count 
                FROM CARACTERISER 
                JOIN $this->table ON $this->table.id = CARACTERISER.id_tag
                WHERE $this->table.nom_tag = :tag";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['tag' => $tag]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['count'] > 0;
    }

    /**
     * donne une liste de tag aléatoire.
     * @param int $ArraySize taille de la liste.
     * @param array $BannedTag liste des tag qui ne doive pas apparètre.
     * @return array
     */
    public function GetListOfTag(int $ArraySize = 1, array $BannedTag = []) : array {
        $sql = "SELECT DISTINCT nom_tag FROM $this->table";
        $params = [];

        if (!empty($BannedTag)){
            $namedPlaceholders = [];
            foreach ($BannedTag as $index => $tag) {
                $placeholder = ':ban' . $index;
                $namedPlaceholders[] = $placeholder;
                $params[$placeholder] = $tag;
            }

            $placeholders = implode(',', $namedPlaceholders);
            $sql .= " WHERE nom_tag NOT IN ($placeholders)";
        }

        $sql .= " ORDER BY RAND() LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$ArraySize, PDO::PARAM_INT);
        foreach ($params as $placeholder => $value) {
            $stmt->bindValue($placeholder, $value, PDO::PARAM_STR);
        }
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $list = [];
        foreach ($result as $row) {
            $list[] = (string)$row['nom_tag'];
        }

        return $list;
    }

    
}
