<?php

class Objet_Panier extends Model {

    protected $table = "Objet_Panier";

    public function getByPanier($idPanier) {
        $stmt = $this->db->prepare("SELECT * FROM $this->table WHERE id_panier = ?");
        $stmt->execute([$idPanier]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function removeTomeFromPanier($idTome, $idUser) {

        $stmt = $this->db->prepare("
            SELECT op.id_objet_panier, op.quantite
            FROM Objet_Panier op
            JOIN COMPOSER_PANIER cp ON op.id_objet_panier = cp.id_objet_panier
            JOIN Panier p ON cp.id_panier = p.id
            WHERE p.id_utilisateur = ? AND op.id_tome = ?
        ");
        $stmt->execute([$idUser, $idTome]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) return;

        if ($result['quantite'] > 1) {
            $stmt = $this->db->prepare("
                UPDATE Objet_Panier SET quantite = quantite - 1
                WHERE id_objet_panier = ?
            ");
            $stmt->execute([$result['id_objet_panier']]);
        } else {
            $stmt = $this->db->prepare("
                DELETE FROM Objet_Panier WHERE id_objet_panier = ?
            ");
            $stmt->execute([$result['id_objet_panier']]);
        }
    }


    public function addTomeAtPanier($idTome, $idUser) {

        $stmt = $this->db->prepare("SELECT id FROM Panier WHERE id_utilisateur = ?");
        $stmt->execute([$idUser]);
        $panier = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$panier) return false;

        $idPanier = $panier['id'];

        $stmt = $this->db->prepare("
            SELECT op.id_objet_panier, op.quantite
            FROM Objet_Panier op
            JOIN COMPOSER_PANIER cp ON op.id_objet_panier = cp.id_objet_panier
            WHERE cp.id_panier = ? AND op.id_tome = ?
        ");
        $stmt->execute([$idPanier, $idTome]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $stmt = $this->db->prepare("
                UPDATE Objet_Panier 
                SET quantite = quantite + 1 
                WHERE id_objet_panier = ?
            ");
            $stmt->execute([$result['id_objet_panier']]);
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO Objet_Panier (quantite, id_tome)
                VALUES (1, ?)
            ");
            $stmt->execute([$idTome]);

            $idObjet = $this->db->lastInsertId();

            $stmt = $this->db->prepare("
                INSERT INTO COMPOSER_PANIER (id_panier, id_objet_panier)
                VALUES (?, ?)
            ");
            $stmt->execute([$idPanier, $idObjet]);
        }

        return true;
    }

    public function deleteTomeFromPanier($idTome, $idUser) {
        $stmt = $this->db->prepare("
            SELECT op.id_objet_panier
            FROM Objet_Panier op
            JOIN COMPOSER_PANIER cp ON op.id_objet_panier = cp.id_objet_panier
            JOIN Panier p ON cp.id_panier = p.id
            WHERE p.id_utilisateur = ? AND op.id_tome = ?
        ");
        $stmt->execute([$idUser, $idTome]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $stmt = $this->db->prepare("DELETE FROM Objet_Panier WHERE id_objet_panier = ?");
            $stmt->execute([$result['id_objet_panier']]);
        }

        return true;
    }

    public function updateQuantiteTome($idTome, $idUser, $quantite) {

        if ($quantite <= 0) {
            return $this->deleteTomeFromPanier($idTome, $idUser);
        }

        $stmt = $this->db->prepare("
            SELECT op.id_objet_panier
            FROM Objet_Panier op
            JOIN COMPOSER_PANIER cp ON op.id_objet_panier = cp.id_objet_panier
            JOIN Panier p ON cp.id_panier = p.id
            WHERE p.id_utilisateur = ? AND op.id_tome = ?
        ");
        $stmt->execute([$idUser, $idTome]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $stmt = $this->db->prepare("
                UPDATE Objet_Panier 
                SET quantite = :quantite 
                WHERE id_objet_panier = :idobjet
            ");
            $stmt->execute([
                'quantite' => $quantite,
                'idobjet' => $result['id_objet_panier']
            ]);
        }

        return true;
    }

    public function getPanierByUserId($idUser) {
        $stmt = $this->db->prepare("
            SELECT op.*, t.price, t.numero_volume, t.quantite_stock 
            FROM Objet_Panier op
            JOIN COMPOSER_PANIER cp ON op.id_objet_panier = cp.id_objet_panier
            JOIN Panier p ON cp.id_panier = p.id
            JOIN Tome t ON op.id_tome = t.id
            WHERE p.id_utilisateur = ?
        ");
        $stmt->execute([$idUser]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}