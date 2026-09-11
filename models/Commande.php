<?php

class Commande extends Model {

    protected $table = "Commande";

    public function all($userId){
        $stmt = $this->db->prepare("SELECT * FROM $this->table WHERE id_utilisateur = :id");
        $stmt->execute(['id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCommandByIdUserAndCommand($id_user, $id_command){
        $stmt = $this->db->prepare("SELECT * FROM $this->table WHERE id_utilisateur = :id AND id = :id_commande");
        $stmt->execute([
            'id' => $id_user,
            'id_commande' => $id_command
            ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCommandById($id_command) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id_command]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function userCommande($userId){
        $stmt = $this->db->prepare("SELECT id FROM $this->table WHERE id_utilisateur = :id");
        $stmt->execute(['id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function userCommandeNotPaid($userId){
        $stmt = $this->db->prepare("SELECT id FROM $this->table WHERE id_utilisateur = :id AND statut = 'En attente de paiement'");
        $stmt->execute(['id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? $result['id'] : false;
    }

    public function create($prix_total, $id_utilisateur, $status = 'En attente de livraison') {
        $sql = "INSERT INTO Commande (date_commande, statut, montant_total, id_utilisateur) 
                VALUES (NOW(), :statut, :montant_total, :id_utilisateur)";
                
        $stmt = $this->db->prepare($sql);
        
        $stmt->execute([
            'montant_total' => $prix_total,
            'statut' => $status,
            'id_utilisateur' => $id_utilisateur,
        ]);
        
        return $this->db->lastInsertId(); 
    }

    public function updateStatut($id_commande, $nouveau_statut, $statut_requis) {
        $sql = "UPDATE {$this->table} 
                SET statut = :nouveau_statut 
                WHERE id = :id_commande AND statut = :statut_requis";
                
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            'nouveau_statut' => $nouveau_statut,
            'id_commande' => $id_commande,
            'statut_requis' => $statut_requis
        ]);
    }

    public function updateMontantTotal($id_commande, $prix_total) {
        $sql = "UPDATE {$this->table} SET montant_total = :montant_total WHERE id = :id_commande";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'montant_total' => $prix_total,
            'id_commande' => $id_commande
        ]);
    }

    public function cancelCommande($id_commande, $id_utilisateur) {
        $sql = "UPDATE {$this->table} 
                SET statut = 'Annulée' 
                WHERE id = :id AND id_utilisateur = :id_user AND statut = 'En attente de paiement'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $id_commande,
            'id_user' => $id_utilisateur
        ]);
        return $stmt->rowCount() > 0;
    }

    public function userHaveCommande($id_utilisateur, $id_commande) {
        $stmt = $this->db->prepare("SELECT id FROM {$this->table} WHERE id_utilisateur = :id_user AND id = :id_cmd");
        $stmt->execute([
            'id_user' => $id_utilisateur, 
            'id_cmd' => $id_commande
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    public function deleteCommande($id_commande, $id_utilisateur) {
        $sql = "DELETE FROM {$this->table} 
                WHERE id = :id 
                AND id_utilisateur = :id_user 
                AND statut = 'Annulée'";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $id_commande,
            'id_user' => $id_utilisateur
        ]);

        return $stmt->rowCount() > 0;
    }

    public function getAllCommandesAdmin() {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} ORDER BY date_commande DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function adminCancelCommande($id_commande) {
        $sql = "UPDATE {$this->table} 
                SET statut = 'Annulée' 
                WHERE id = :id AND statut = 'En attente de paiement'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id_commande]);
        return $stmt->rowCount() > 0;
    }

    public function adminDeleteCommande($id_commande) {
        $sql = "DELETE FROM {$this->table} 
                WHERE id = :id 
                AND statut = 'Annulée'";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id_commande]);
        return $stmt->rowCount() > 0;
    }

    public function adminDeliverCommande($id_commande) {
        $sql = "UPDATE {$this->table} 
                SET statut = 'Livrée' 
                WHERE id = :id AND statut = 'En attente de livraison'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id_commande]);
        return $stmt->rowCount() > 0;
    }
}