<?php

class Model {

    protected $db;
    protected $nsfwTags = ['Hentai', 'Ecchi', 'Erotica'];
    protected $table;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getNsfwTags(){
        return $this->nsfwTags;
    }

    /**
     * Récupère un avis par son identifiant.
     *
     * @param int $id Identifiant unique (id)
     *
     * @return array Tableau associatif contenant (ou vide si non trouvé)
     *
     * @throws PDOException En cas d'erreur lors de la requête SQL
     */
    public function getById($id){
        $stmt = $this->db->prepare("SELECT * FROM $this->table WHERE id = ?");
        $stmt->execute([$id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Insère une nouvelle ligne dans la table.
     *
     * @param array $data Tableau associatif des colonnes et valeurs
     *
     * @return int Identifiant de la nouvelle ligne insérée
     *
     * @throws PDOException En cas d'erreur SQL
     */
    public function insert(array $data): int
    {
        if (empty($data)) {
            throw new InvalidArgumentException("Aucune donnée fournie pour l'insertion.");
        }

        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');

        $stmt = $this->db->prepare("INSERT INTO {$this->table} (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $placeholders) . ")");

        $stmt->execute(array_values($data));
        return true;
    }

    /**
     * Supprime un avis à partir de son identifiant.
     *
     * @param int $id Identifiant unique (id)
     *
     * @return bool True si un avis a été supprimé, false sinon
     *
     * @throws PDOException En cas d'erreur lors de la requête SQL
     */
    public function deletById($id){
        $stmt = $this->db->prepare("DELETE FROM $this->table WHERE `id` = ?");
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Met à jour partiellement un avis en fonction de son identifiant.
     *
     * Cette méthode permet de modifier uniquement certains champs .
     *
     * @param int   $id   Identifiant unique de l'avis (id)
     * @param array $data Tableau associatif des champs à mettre à jour.
     *                    Exemple :
     *                    [
     *                      'note' => 4,
     *                      'commentaire' => 'Super tome',
     *                      'id_tome' => 2,
     *                      'id_utilisateur' => 10
     *                    ]
     *
     * @return bool True si au moins une ligne a été modifiée, false sinon
     *
     * @throws PDOException En cas d'erreur lors de l'exécution de la requête SQL.
     */
    public function updateById(int $id,array $data): bool{

        $setParts = [];
        $params = [];

        foreach ($data as $key => $value) {
                $setParts[] = "$key = :$key";
                $params[":$key"] = $value;
        }

        $params[':id_targetPHPSQL'] = $id;

        // Si aucun champ à mettre à jour
        if (empty($setParts)) {
            return false;
        }

        $stmt = $this->db->prepare("UPDATE $this->table SET " . implode(', ', $setParts) . " WHERE id = :id_targetPHPSQL");
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }

    
    /**
     * Retourne le nombre de lignes de la table.
     *
     * @return int Nombre de lignes
     *
     * @throws PDOException En cas d'erreur lors de l'exécution de la requête SQL.
     */
    public function getLength(): int{
        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM {$this->table}");
        $stmt->execute();

        // fetch() retourne un tableau associatif
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) $result['total'];
    }

}