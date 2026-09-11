<?php

class Type_Edition extends Model {

    protected $table = "Type_Edition";

    public function findAll() {
        return $this->db->query("SELECT * FROM $this->table")->fetchAll(PDO::FETCH_ASSOC);
    }
}