<?php
include "connexion.php";

// Connexion à la base de données
$database = new Database();
$db = $database->getConnection();

if (isset($_GET['matricule'])) {
    $matricule = $_GET['matricule'];

    // Requête pour récupérer les informations de l'étudiant
    $sql = "SELECT * FROM etudiants WHERE matricule = :matricule";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':matricule', $matricule);
    $stmt->execute();

    $etudiant = $stmt->fetch(PDO::FETCH_ASSOC);

    // Retourner les données sous forme de JSON
    if ($etudiant) {
        echo json_encode($etudiant);
    } else {
        echo json_encode(['error' => 'Étudiant non trouvé']);
    }
} else {
    echo json_encode(['error' => 'Matricule non fourni']);
}
