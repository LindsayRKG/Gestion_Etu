<?php
include "connexion.php";

// Exemple d'utilisation
$database = new Database();
$db = $database->getConnection();

// Vérifier si l'ID est passé dans l'URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    // Récupérer les informations de l'étudiant
    $sql = "SELECT * FROM etudiants WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $etudiant = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$etudiant) {
        echo "Étudiant non trouvé.";
        exit;
    }
} else {
    echo "Aucun ID d'étudiant fourni.";
    exit;
}

// Mise à jour des données après soumission du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $Email = $_POST['Email'];
    $dateNaiss=$_POST['dateNaiss'];
    $nomPrt=$_POST['nomPrt'];
    $emailPrt=$_POST['emailPrt'];
    

    $sql = "UPDATE etudiants SET nom = :nom, prenom = :prenom, Email = :Email, dateNaiss= :dateNaiss, nomPrt= :nomPrt, emailPrt= :emailPrt WHERE id = :id";
    $stmt = $db->prepare($sql);

    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':prenom', $prenom);
    $stmt->bindParam(':Email', $Email);
    $stmt->bindParam(':dateNaiss', $dateNaiss);
    $stmt->bindParam(':nomPrt', $nomPrt); 
    $stmt->bindParam(':emailPrt', $emailPrt);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "Étudiant mis à jour avec succès.";
        header("Location: etd.php");
        exit;
    } else {
        echo "Erreur lors de la mise à jour.";
    }
}
?>