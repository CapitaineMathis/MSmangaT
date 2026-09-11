<?php

class Panier extends Model {

    protected $table = "Panier";

    

    public function create($idUser) {
        $stmt = $this->db->prepare("INSERT INTO Panier (id_utilisateur) VALUES (?)");
        $stmt->execute([$idUser]);
    }

    public function getByUser($idUser) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id_utilisateur = ?");
        $stmt->execute([$idUser]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function clearPanier($idUser) {
        $stmt = $this->db->prepare("SELECT id FROM {$this->table} WHERE id_utilisateur = ?");
        $stmt->execute([$idUser]);
        $panier = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($panier && isset($panier['id'])) {
            $idPanier = $panier['id'];
            
            $stmt = $this->db->prepare("SELECT id_objet_panier FROM COMPOSER_PANIER WHERE id_panier = ?");
            $stmt->execute([$idPanier]);
            $objets = $stmt->fetchAll(PDO::FETCH_COLUMN);

            if (!empty($objets)) {
                $stmt = $this->db->prepare("DELETE FROM COMPOSER_PANIER WHERE id_panier = ?");
                $stmt->execute([$idPanier]);

                $inQuery = implode(',', array_fill(0, count($objets), '?'));
                $stmt = $this->db->prepare("DELETE FROM Objet_Panier WHERE id_objet_panier IN ($inQuery)");
                $stmt->execute($objets);
            }
        }
    }
}