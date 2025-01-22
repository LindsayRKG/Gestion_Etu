<?php
session_start();

require_once 'Classes/Database.php';
require_once 'Classes/Etudiant.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérifier si l'étudiant est connecté, sinon rediriger vers la page de connexion
if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit();
}

// Créer une instance de Database et obtenir la connexion
$database = new Database();
$db = $database->getConnection();

// Créer une instance de Etudiant
$etudiant = new Etudiant($db);

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $matricule = $_SESSION['student_id']; // Utiliser l'ID de l'étudiant de la session
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Récupérer les informations de l'étudiant pour vérifier le mot de passe actuel
    $etudiant->matricule = $matricule;
    $stmt = $etudiant->getStudentByMatricule();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier si le mot de passe actuel est correct
    if ($result && $current_password === $result['pass']) {
        // Vérifier si les nouveaux mots de passe correspondent
        if ($new_password === $confirm_password) {
            // Mettre à jour le mot de passe dans la base de données
            $etudiant->pass = $new_password;

            if ($etudiant->updatePassword()) {
                $message = "Mot de passe mis à jour avec succès!";
            } else {
                $message = "Erreur lors de la mise à jour du mot de passe.";
            }
        } else {
            $message = "Les nouveaux mots de passe ne correspondent pas.";
        }
    } else {
        $message = "Le mot de passe actuel est incorrect.";
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Changement de mot de passe</title>
</head>
<body>
    <h2>Changer le mot de passe</h2>

    <?php
    if (isset($message)) {
        echo "<p style='color: red;'>$message</p>";
    }
    ?>

    <form method="POST" action="">
        <label>Mot de passe actuel :</label>
        <input type="password" name="current_password" required><br><br>

        <label>Nouveau mot de passe :</label>
        <input type="password" name="new_password" required><br><br>

        <label>Confirmer le nouveau mot de passe :</label>
        <input type="password" name="confirm_password" required><br><br>

        <button type="submit">Mettre à jour le mot de passe</button>
    </form>
</body>
</html>
