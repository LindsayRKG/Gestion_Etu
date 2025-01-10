<?php
session_start();
require_once 'Classes/Database.php';
require_once 'Classes/Etudiant.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username']; // Matricule utilisé comme nom d'utilisateur
    $password = $_POST['password'];

    $database = new Database();
    $conn = $database->getConnection();

    // Vérifie les informations dans la table `users`
    $query = "SELECT * FROM user WHERE nom = :username";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Vérification du mot de passe
        if (password_verify($password, $user['pass'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['nom'];

            // Vérifier si c'est la première connexion
            $query = "SELECT * FROM etudiants WHERE id = :user_id";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':user_id', $user['id'], PDO::PARAM_INT);
            $stmt->execute();
            $etudiant = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($etudiant) {
                $_SESSION['etudiant_id'] = $etudiant['id'];
                $_SESSION['matricule'] = $etudiant['matricule'];
                $_SESSION['nom'] = $etudiant['nom'];
                $_SESSION['prenom'] = $etudiant['prenom'];

                if (!$user['password_changed']) {
                    header('Location: change_password.php');
                    exit();
                }

                // Redirige vers le tableau de bord étudiant
                header('Location: dashEtud.php');
                exit();
            } else {
                $error = "Aucun étudiant associé à ce compte.";
            }
        } else {
            $error = "Mot de passe incorrect.";
        }
    } else {
        $error = "Utilisateur non trouvé.";
    }
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
