<?php
require_once "get_etudiant1.php";
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['matricule'];
    $password = $_POST['pass'];

    $student = new Student();
    $user = $student->login($email, $password);

    if ($user) {
        // Stocker les informations de l'utilisateur dans la session
        $_SESSION['user'] = [
            'id' => $user['id'],
            'matricule' => $user['matricule'],
            'email' => $user['email'],
            'pass' => $user['pass'],
        ];

        // Redirection selon la condition du mot de passe
        if ($user['pass'] === $user['matricule']) {
            header("Location: change_password.php");
        } else {
            header("Location: dashEtud.php");
        }
        exit();
    } else {
        $error = "Email ou mot de passe incorrect.";
        $_SESSION['error'] = $error;
        header("Location: index.php");
        exit();
    }
}
?>
