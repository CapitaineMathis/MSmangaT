<?php

class Avis extends Model {
 
    protected $table = "Avis";

    /**
     * Récupère tous les avis associés à un tome donné.
     *
     * @param int $idTome Identifiant du tome
     *
     * @return array Tableau associatif contenant la liste des avis
     *
     * @throws PDOException En cas d'erreur lors de la requête SQL
     */
    public function getByTome($idTome) {
        $stmt = $this->db->prepare("SELECT * FROM $this->table WHERE id_tome = ?");
        $stmt->execute([$idTome]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère tous les note associés à un tome donné.
     *
     * @param int $idTome Identifiant du tome
     *
     * @return array Tableau associatif contenant la liste des note
     */
    public function getNoteByTome($idTome) {
        $stmt = $this->db->prepare("SELECT note FROM $this->table WHERE id_tome = ?");
        $stmt->execute([$idTome]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ajoute ou met à jour l'avis d'un utilisateur sur un tome.
     *
     * @param int $idUser Identifiant de l'utilisateur
     * @param int $idTome Identifiant du tome
     * @param string $commentaire Le texte de l'avis
     * @param int $note La note donnée (de 1 à 5)
     *
     * @return bool Vrai si l'opération a réussi, Faux sinon
     */
    public function addAvis($idUser, $idTome, $commentaire, $note) {
        $stmt = $this->db->prepare("SELECT id FROM $this->table WHERE id_tome = ? AND id_utilisateur = ?");
        $stmt->execute([$idTome, $idUser]);
        $avisExistant = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($avisExistant) {
            $sql = "UPDATE $this->table 
                    SET note = ?, commentaire = ?, date_creation = CURRENT_DATE 
                    WHERE id = ?";
            $stmtUpdate = $this->db->prepare($sql);
            return $stmtUpdate->execute([$note, $commentaire, $avisExistant['id']]);
        } else {
            $sql = "INSERT INTO $this->table (note, commentaire, id_tome, id_utilisateur) 
                    VALUES (?, ?, ?, ?)";
            $stmtInsert = $this->db->prepare($sql);
            return $stmtInsert->execute([$note, $commentaire, $idTome, $idUser]);
        }
    }

}