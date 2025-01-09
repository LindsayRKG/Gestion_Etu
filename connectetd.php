<?php
session_start();
include "connexion.php";

// Exemple d'utilisation
$database = new Database();
$conn = $database->getConnection();

if ($conn) {
    echo "Connexion réussie à la base de données.";
     // Récupération des données du formulaire
  $adresse = $_POST['matricule'];
  $pass = $_POST['pass'];
  
  // Requête SQL pour vérifier les informations de connexion
  $stmt = $conn->prepare("SELECT matricule, pass FROM `etudiants` WHERE matricule=:matricule AND pass=:pass");
  $stmt->bindParam(':matricule', $adresse);
  $stmt->bindParam(':pass', $pass);
  $stmt->execute();
  $user = $stmt->fetch(PDO::FETCH_ASSOC);
  
  // Vérification si les informations sont correctes
  if ($user && password_verify($pass, $user['pass'])) {
    // Redirection vers la page d'accueil
    $_SESSION['user_id'] = $user['matricule'];
    $_SESSION['pass'] = $user['pass'];
    header("Location: dashEtud.php");
    exit();
  } else {
     header("Location: index.php");
     exit();
  }
} else {
    echo "Échec de la connexion.";
}

?>