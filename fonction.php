<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'Classes/Database.php';


 class Fonction {

    private $conn;
    

    public function __construct($db) {
        $this->conn = $db;
    }
    public function updateStudentPassword($student_id, $new_password, $confirm_password) {
        // Initialisation des messages
        $response = [
            'success' => false,
            'message' => ''
        ];

        // Validation des mots de passe
        if ($new_password !== $confirm_password) {
            $response['message'] = "Les mots de passe ne correspondent pas.";
            return $response;
        }

        if (strlen($new_password) < 8) {
            $response['message'] = "Le mot de passe doit contenir au moins 8 caractères.";
            return $response;
        }

        try {
            // Préparation de la requête pour mettre à jour le mot de passe
            $query = "UPDATE etudiants SET pass = :pass WHERE id = :id";
            $stmt = $this->conn->prepare($query);

            // Génération du hachage du mot de passe
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Association des paramètres
            $stmt->bindParam(":pass", $new_password, PDO::PARAM_STR);
            $stmt->bindParam(":id", $student_id, PDO::PARAM_INT);

            // Exécution de la requête
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "Mot de passe mis à jour avec succès.";
            } else {
                $response['message'] = "Une erreur s'est produite lors de la mise à jour. Veuillez réessayer.";
            }
        } catch (PDOException $e) {
            $response['message'] = "Erreur : " . $e->getMessage();
        }

        return $response;
    }
}
