<?php

class Travailler_Sur_Serie extends Model {

    protected $table = "TRAVAILLER_SUR_SERIE";

    public function getBySerie($idSerie) {
        $stmt = $this->db->prepare("
            SELECT c.* FROM contributor c
            JOIN TRAVAILLER_SUR_SERIE t ON c.id_contributeur = t.id_contributeur
            WHERE t.id_serie = ?
        ");
        $stmt->execute([$idSerie]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}