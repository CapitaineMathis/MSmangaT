<?php

class Utilisateur extends Model {

    protected $table = "Utilisateur";

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM $this->table WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM $this->table WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO $this->table (username, email, tel, password, date_inscription)
            VALUES (?, ?, ?, ?, NOW())
        ");
        return $stmt->execute(array_values($data));
    }

    public function createWithoutTel($data) {
        $stmt = $this->db->prepare("
            INSERT INTO $this->table (username, email, password, date_inscription)
            VALUES (?, ?, ?, NOW())
        ");
        return $stmt->execute(array_values($data));
    }

    public function addCommandInfo($nom, $prenom, $tel, $numero_rue, $nom_rue, $ville, $code_postal, $id, $complement = null) {
        if (!$complement) {
            $stmt = $this->db->prepare("
                UPDATE $this->table 
                SET nom = :nom, prenom = :prenom, tel = :tel, numero_rue = :numero_rue, nom_rue = :nom_rue, ville = :ville, code_postal = :code_postal
                WHERE id = :id
            ");
            return $stmt->execute([
                'nom' => $nom,
                'prenom' => $prenom,
                'tel' => $tel,
                'numero_rue' => $numero_rue,
                'nom_rue' => $nom_rue,
                'ville' => $ville,
                'code_postal' => $code_postal,
                'id' => $id
            ]);
        }
        else {
            $stmt = $this->db->prepare("
            UPDATE $this->table 
            SET nom = :nom, prenom = :prenom, tel = :tel, numero_rue = :numero_rue, nom_rue = :nom_rue, ville = :ville, code_postal = :code_postal, complement_adresse = :complement
            WHERE id = :id
            ");
            return $stmt->execute([
                'nom' => $nom,
                'prenom' => $prenom,
                'tel' => $tel,
                'numero_rue' => $numero_rue,
                'nom_rue' => $nom_rue,
                'ville' => $ville,
                'code_postal' => $code_postal,
                'complement' => $complement,
                'id' => $id
            ]);
        }

    }
}