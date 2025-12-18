<?php
session_start();

require_once 'Classes/Database.php';
require_once 'Classes/Etudiant.php';

$database = new Database();
$db = $database->getConnection();

$etudiant = new Etudiant($db);

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $matricule = $_POST['username'];
    $password = $_POST['password'];

    if ($etudiant->login($matricule, $password)) {
        $_SESSION['student_id'] = $etudiant->id;
        header('Location: dashEtud.php');
        exit();
    } else {
        $error = "Matricule ou mot de passe incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: flex-start; /* Alignement en haut */
            height: 100vh;
            margin: 0;
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }

        .wrapper {
            width: 100%;
            max-width: 400px;
            position: relative;
            perspective: 1000px;
            margin-top: 10vh; /* Déplacer le container plus haut */
            transform: translateY(-50%); /* Ajuster le centrage vertical */
        }

        .form-container {
            width: 100%;
            position: absolute;
            transition: transform 0.8s ease-in-out;
            backface-visibility: hidden;
            background: #fff;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .form-container.front {
            z-index: 2;
        }

        .form-container.back {
            transform: rotateY(180deg);
        }

        .wrapper.flipped .front {
            transform: rotateY(180deg);
        }

        .wrapper.flipped .back {
            transform: rotateY(0);
        }

        h1, h2 {
            font-size: 24px;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        .input-box {
            position: relative;
            margin-bottom: 20px;
        }

        .input-box input {
            width: calc(100% - 50px); /* Ajuster la largeur pour ne pas dépasser */
            padding: 10px 40px;
            border: 1px solid #ccc;
            border-radius: 24px;
            outline: none;
            font-size: 16px;
            color: #333;
        }

        .input-box i {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            color: #aaa;
            font-size: 18px;
        }

        .btn {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 24px;
            background-color: #685cfe;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn:hover {
            background-color: #4b45c6;
        }

        .register-link {
            text-align: center;
            margin-top: 10px;
        }

        .register-link a {
            text-decoration: none;
            color: #685cfe;
            transition: color 0.3s;
        }

        .register-link a:hover {
            color: #4b45c6;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <form action="recuperation.php" method="post" id="connexion" class="form-container front">
            <h1>Connexion Administrateur</h1>
            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            <div class="input-box">
                <input type="text" name="nom" id="nom" placeholder="Nom utilisateur" required>
                <i class="fas fa-user"></i>
            </div>

            <div class="input-box">
                <input type="password" name="pass" id="passe" placeholder="Mot de Passe" required>
                <i class="fas fa-lock"></i>
            </div>

            <div class="register-link">
                <p><a href="#" id="btn_formulaire_form">Se connecter en tant qu'étudiant</a></p>
            </div>
            <button type="submit" class="btn">Connexion</button>
        </form>

        <form method="POST" action="" id="formulaire_enregistrement" class="form-container back">
            <h2>Connexion Étudiant</h2>
            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            <div class="input-box">
                <input type="text" name="username" placeholder="Nom d'utilisateur (Matricule)" required>
                <i class="fas fa-user"></i>
            </div>

            <div class="input-box">
                <input type="password" name="password" placeholder="Mot de passe" required>
                <i class="fas fa-lock"></i>
            </div>

            <div class="register-link">
                <p><a href="#" id="btn_login_form">Se connecter en tant qu'admin</a></p>
            </div>
            <button type="submit" class="btn">Se connecter</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            const wrapper = $('.wrapper');

            $("#btn_formulaire_form").click(function(e) {
                e.preventDefault();
                wrapper.addClass('flipped');
            });

            $("#btn_login_form").click(function(e) {
                e.preventDefault();
                wrapper.removeClass('flipped');
            });
        });
    </script>
</body>

</html>