<?php

class Contributor extends Model {

    protected $table = "contributor";

    public function findAll() {
        return $this->db->query("SELECT * FROM $this->table")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM $this->table WHERE id_contributeur = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO $this->table (nom, prenom, date_naissance, date_deces, biographie, chemin_image, nationalite, site_web, role)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute(array_values($data));
    }

    public function delete($id) {
        return $this->db->prepare("DELETE FROM $this->table WHERE id_contributeur = ?")->execute([$id]);
    }
}