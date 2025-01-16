<?php
session_start();
require_once'Classes/Database.php';


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Exemple d'utilisation
$database = new Database();
$conn = $database->getConnection();

if ($conn) {
    echo "Connexion réussie à la base de données.";
     // Récupération des données du formulaire
  $adresse = $_POST['nom'];
  $pass = $_POST['pass'];
  
  // Requête SQL pour vérifier les informations de connexion
  $stmt = $conn->prepare("SELECT nom, pass FROM `user` WHERE nom=:nom AND pass=:pass");
  $stmt->bindParam(':nom', $adresse);
  $stmt->bindParam(':pass', $pass);
  $stmt->execute();
  $user = $stmt->fetch(PDO::FETCH_ASSOC);
  
  // Vérification si les informations sont correctes
  if ($user && password_verify($pass, $user['pass'])) {
    // Redirection vers la page d'accueil
    $_SESSION['user_id'] = $user['nom'];
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