<?php
session_start();

require_once 'Classes/Database.php';
require_once 'Classes/Etudiant.php';

$database = new Database();
$db = $database->getConnection();

$etudiant = new Etudiant($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $matricule = $_POST['username'];
    $password = $_POST['password'];

    if ($etudiant->login($matricule, $password)) {
        $_SESSION['student_id'] = $etudiant->id;

        // if (!$etudiant->password_changed) {
        //     header('Location: change_password.php');
        // } else {
            header('Location: dashEtud.php');
        }
        exit();
    } else {
        $error = "Matricule ou mot de passe incorrect.";
    }

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Étudiant</title>
</head>
<body>
    <h2>Connexion Étudiant</h2>
    <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
    <form method="POST" action="">
        <label>Nom d'utilisateur (Matricule) :</label>
        <input type="text" name="username" required>
        <label>Mot de passe :</label>
        <input type="password" name="password" required>
        <button type="submit">Se connecter</button>
    </form>
</body>
</html>
