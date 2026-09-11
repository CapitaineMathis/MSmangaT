<?php

class Maison_Edition extends Model {

    protected $table = "Maison_Edition";

    public function findAll() {
        return $this->db->query("SELECT * FROM $this->table")->fetchAll(PDO::FETCH_ASSOC);
    }
}