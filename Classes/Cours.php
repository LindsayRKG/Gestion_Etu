<?php
class Cours {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Ajouter un cours
    public function ajouterCours($data) {
        // Insertion dans la table cours avec la classe (niveau) fournie directement
        $query = "INSERT INTO cours (nom, niveau, credit) VALUES (:nom, :niveau, :credit)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute($data);
    }

    // Lister les cours avec le niveau
    public function listerCours() {
        $query = "SELECT * FROM cours";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
