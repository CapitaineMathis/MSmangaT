<?php

class Objet_Commande extends Model {

    protected $table = "Objet_Commande";

    public function getByCommande($idCommande) {
        $stmt = $this->db->prepare("SELECT * FROM $this->table WHERE id_commande = ?");
        $stmt->execute([$idCommande]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addObjectToCommande($quantite, $prix_unitaire, $id_tome, $id_commande) {
        $sql = "INSERT INTO Objet_Commande (quantite, prix_achat, id_tome, id_commande) 
                VALUES (:quantite, :prix_unitaire, :id_tome, :id_commande)";
                
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            'quantite' => $quantite,
            'prix_unitaire' => $prix_unitaire,
            'id_tome' => $id_tome,
            'id_commande' => $id_commande
        ]);
    }

    public function clearByCommande($idCommande) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id_commande = ?");
        return $stmt->execute([$idCommande]);
    }
}