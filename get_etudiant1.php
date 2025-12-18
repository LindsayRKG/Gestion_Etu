<?php
require_once 'Classes/Database.php';

// Connexion à la base de données




class Student {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Vérifier si les informations de connexion sont correctes
    public function login($email, $password) {
        try {
            $query = "SELECT * FROM etudiants WHERE matricule = :matricule";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':matricule', $email, PDO::PARAM_STR);
            $stmt->execute();
    
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user && password_verify($password, $user['pass'])) {
                return $user; // Retourne les données de l'utilisateur si le mot de passe correspond
            }
        } catch (PDOException $e) {
            error_log("Erreur lors de la connexion : " . $e->getMessage());
        }
        return false; // Retourne false si l'authentification échoue
    }
    
}

