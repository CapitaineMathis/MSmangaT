<?php

class Tome extends Model {

    protected $table = "Tome";

    public function findAll() {
        return $this->db->query("SELECT * FROM $this->table")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM $this->table WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTomeNumberInSerie($idSerie) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM $this->table WHERE id_serie = ?");
        $stmt->execute([$idSerie]);
        return $stmt->fetchColumn();
    }

    public function findBySerie($idSerie) {
        $stmt = $this->db->prepare("SELECT * FROM $this->table WHERE id_serie = ?");
        $stmt->execute([$idSerie]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTomeVolume($idTome) {
        $stmt = $this->db->prepare("SELECT numero_volume FROM $this->table WHERE id = ?");
        $stmt->execute([$idTome]);
        return $stmt->fetchColumn(); 
    }

    public function reduceStockByCommande($idCommande) {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} t
            INNER JOIN Objet_Commande oc ON t.id = oc.id_tome
            SET t.quantite_stock = t.quantite_stock - oc.quantite
            WHERE oc.id_commande = ?
        ");
        return $stmt->execute([$idCommande]);
    }

    public function getTomeImageUrlAndSerieTitleById($idTome) {
        $stmt = $this->db->prepare("SELECT Tome.chemin_image, titre, numero_volume FROM $this->table JOIN Serie_Manga ON id_serie = Serie_Manga.id WHERE Tome.id = :id");
        $stmt->execute(['id' => $idTome]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }
}