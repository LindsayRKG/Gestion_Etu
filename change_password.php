<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

require_once 'Classes/Database.php';
require_once 'Classes/Fonction.php';

$message = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données du formulaire
    $student_id = $_SESSION['user']['id'];
    $new_password = $_POST['pass'];
    $confirm_password = $_POST['confirm_password'];

    // Connexion à la base de données
    $db = new Database();
    $conn = $db->getConnection();

    // Instance de la classe Fonction
    $fonction = new Fonction($conn);

    // Appel de la méthode updateStudentPassword
    $result = $fonction->updateStudentPassword($student_id, $new_password, $confirm_password);
    $message = $result['message'];
    $success = $result['success'];

    if ($success) {
        // Redirection après mise à jour réussie
        header("Location: dashEtud.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Changer le mot de passe</title>
</head>
<body>
    <h2>Changer le mot de passe</h2>

    <?php if ($message): ?>
        <p style="color: <?= $success ? 'green' : 'red'; ?>"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Nouveau mot de passe :</label>
        <input type="password" name="pass" required><br><br>

        <label>Confirmer le nouveau mot de passe :</label>
        <input type="password" name="confirm_password" required><br><br>

        <button type="submit">Mettre à jour</button>
    </form>
</body>
</html>
