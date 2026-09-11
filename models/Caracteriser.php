<?php

class Caracteriser extends Model {

    protected $table = "CARACTERISER";

    public function getTagsBySerieId($idSerie) {
        $sql = "SELECT Tag.nom_tag
            FROM {$this->table}
            JOIN Tag ON Tag.id = {$this->table}.id_tag
            WHERE {$this->table}.id_serie = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $idSerie]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}