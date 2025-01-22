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
 } //else {
//     $error = "Matricule ou mot de passe incorrect.";
// }
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
            align-items: center;
            min-height: 51vh; /* Utilisez min-height pour couvrir toute la hauteur de l'écran */
            margin: 0;
            background-color: #171717;
            font-family: 'Poppins', sans-serif;
            overflow: hidden; /* Cache les débordements des reflets */
            position: relative;
        }

        /* Reflets */
        body::before,
        body::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(0, 119, 255, 0.66), transparent 70%);
            filter: blur(100px);
            z-index: 0;
        }

        body::before {
            top: -150px;
            right: -150px;
            background: radial-gradient(circle, rgba(0, 119, 255, 0.67), transparent 70%); /* Reflet bleu */
        }

        body::after {
            bottom: -150px;
            left: -150px;
            background:radial-gradient(circle, rgb(225 156 31), transparent 70%); /* Reflet orange */
        }

        .wrapper {
            width: 100%;
            max-width: 400px;
            perspective: 1000px;
            position: relative;
            z-index: 1; /* Assure que le formulaire est au-dessus des reflets */
        }

        .form-container {
            width: 100%;
            position: absolute;
            transition: transform 0.8s ease-in-out;
            backface-visibility: hidden;
            background: rgba(23, 23, 23, 0.5); /* Fond semi-transparent */
            padding: 2em;
            border-radius: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.1); /* Bordure légère */
            backdrop-filter: blur(10px); /* Effet de flou pour la transparence */
            min-height: 400px; /* Augmentation de la hauteur du formulaire */
            display: flex;
            flex-direction: column;
            justify-content: center; /* Centrer le contenu verticalement */
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
            text-align: center;
            margin: 1em 0;
            color: rgb(255, 255, 255);
            font-size: 1.5em;
        }

        .field {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5em;
            border-radius: 28px;
            padding: 1.2em;
            border: none;
            outline: none;
            color: white;
            background-color: rgba(23, 23, 23, 0.3); /* Champ semi-transparent */
            box-shadow:inset 2px 5px 10px rgb(164 128 95 / 27%);
            margin-bottom: 1.5em; /* Augmentation de l'espace entre les champs */
        }

        .input-icon {
            height: 1.3em;
            width: 1.3em;
            fill: white;
        }

        .input-field {
            background: none;
            border: none;
            outline: none;
            width: 100%;
            color: #d3d3d3;
            font-size: 1em;
        }

        .btn {
            display: flex;
            justify-content: center;
            flex-direction: row;
            margin-top: 2.5em;
        }

        .button {
            --black-700: hsla(0 0% 12% / 1);
            --border_radius: 9999px;
            --transtion: 0.3s ease-in-out;
            --offset: 2px;

            cursor: pointer;
            position: relative;

            display: flex;
            align-items: center;
            gap: 0.5rem;

            transform-origin: center;

            padding: 1rem 2rem;
            background-color: transparent;

            border: none;
            border-radius: var(--border_radius);
            transform: scale(calc(1 + (var(--active, 0) * 0.1)));

            transition: transform var(--transtion);
        }

        .button::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);

            width: 100%;
            height: 100%;
            background-color: var(--black-700);

            border-radius: var(--border_radius);
            box-shadow: inset 0 0.5px hsl(0, 0%, 100%), inset 0 -1px 2px 0 hsl(0, 0%, 0%),
                0px 4px 10px -4px hsla(0 0% 0% / calc(1 - var(--active, 0))),
                0 0 0 calc(var(--active, 0) * 0.375rem) hsl(260 97% 50% / 0.75);

            transition: all var(--transtion);
            z-index: 0;
        }

        .button::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);

            width: 100%;
            height: 100%;
            background-color: hsla(260 97% 61% / 0.75);
            background-image: radial-gradient(
                    at 51% 89%,
                    hsla(266, 45%, 74%, 1) 0px,
                    transparent 50%
                ),
                radial-gradient(at 100% 100%, hsla(266, 36%, 60%, 1) 0px, transparent 50%),
                radial-gradient(at 22% 91%, hsla(266, 36%, 60%, 1) 0px, transparent 50%);
            background-position: top;

            opacity: var(--active, 0);
            border-radius: var(--border_radius);
            transition: opacity var(--transtion);
            z-index: 2;
        }

        .button:is(:hover, :focus-visible) {
            --active: 1;
        }
        .button:active {
            transform: scale(1);
        }

        .button .dots_border {
            --size_border: calc(100% + 2px);

            overflow: hidden;

            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);

            width: var(--size_border);
            height: var(--size_border);
            background-color: transparent;

            border-radius: var(--border_radius);
            z-index: -10;
        }

        .button .dots_border::before {
            content: "";
            position: absolute;
            top: 30%;
            left: 50%;
            transform: translate(-50%, -50%);
            transform-origin: left;
            transform: rotate(0deg);

            width: 100%;
            height: 2rem;
            background-color: white;

            mask: linear-gradient(transparent 0%, white 120%);
            animation: rotate 2s linear infinite;
        }

        @keyframes rotate {
            to {
                transform: rotate(360deg);
            }
        }

        .button .sparkle {
            position: relative;
            z-index: 10;

            width: 1.75rem;
        }

        .button .sparkle .path {
            fill: currentColor;
            stroke: currentColor;

            transform-origin: center;

            color: hsl(0, 0%, 100%);
        }

        .button:is(:hover, :focus) .sparkle .path {
            animation: path 1.5s linear 0.5s infinite;
        }

        .button .sparkle .path:nth-child(1) {
            --scale_path_1: 1.2;
        }
        .button .sparkle .path:nth-child(2) {
            --scale_path_2: 1.2;
        }
        .button .sparkle .path:nth-child(3) {
            --scale_path_3: 1.2;
        }

        @keyframes path {
            0%,
            34%,
            71%,
            100% {
                transform: scale(1);
            }
            17% {
                transform: scale(var(--scale_path_1, 1));
            }
            49% {
                transform: scale(var(--scale_path_2, 1));
            }
            83% {
                transform: scale(var(--scale_path_3, 1));
            }
        }

        .button .text_button {
            position: relative;
            z-index: 10;

            background-image: linear-gradient(
                90deg,
                hsla(0 0% 100% / 1) 0%,
                hsla(0 0% 100% / var(--active, 0)) 120%
            );
            background-clip: text;

            font-size: 1rem;
            color: transparent;
        }

        .register-link {
            text-align: center;
            margin-top: 1em;
        }

        .register-link a {
            text-decoration: none;
            color: rgb(29, 65, 138);
            transition: color 0.3s;
        }

        .register-link a:hover {
            color:rgb(69, 110, 198);
        }

        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 1em;
        }

        /* Loader CSS */
        .loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .loader {
            --size: 250px;
            --duration: 2s;
            --logo-color: grey;
            --background: linear-gradient(
                0deg,
                rgba(50, 50, 50, 0.2) 0%,
                rgba(100, 100, 100, 0.2) 100%
            );
            height: var(--size);
            aspect-ratio: 1;
            position: relative;
        }

        .loader .box {
            position: absolute;
            background: rgba(100, 100, 100, 0.15);
            background: var(--background);
            border-radius: 50%;
            border-top: 1px solid rgba(100, 100, 100, 1);
            box-shadow: rgba(0, 0, 0, 0.3) 0px 10px 10px -0px;
            backdrop-filter: blur(5px);
            animation: ripple var(--duration) infinite ease-in-out;
        }

        .loader .box:nth-child(1) {
            inset: 40%;
            z-index: 99;
        }

        .loader .box:nth-child(2) {
            inset: 30%;
            z-index: 98;
            border-color: rgba(100, 100, 100, 0.8);
            animation-delay: 0.2s;
        }

        .loader .box:nth-child(3) {
            inset: 20%;
            z-index: 97;
            border-color: rgba(100, 100, 100, 0.6);
            animation-delay: 0.4s;
        }

        .loader .box:nth-child(4) {
            inset: 10%;
            z-index: 96;
            border-color: rgba(100, 100, 100, 0.4);
            animation-delay: 0.6s;
        }

        .loader .box:nth-child(5) {
            inset: 0%;
            z-index: 95;
            border-color: rgba(100, 100, 100, 0.2);
            animation-delay: 0.8s;
        }

        .loader .logo {
            position: absolute;
            inset: 0;
            display: grid;
            place-content: center;
            padding: 30%;
        }

        .loader .logo svg {
            fill: var(--logo-color);
            width: 100%;
            animation: color-change var(--duration) infinite ease-in-out;
        }

        @keyframes ripple {
            0% {
                transform: scale(1);
                box-shadow: rgba(0, 0, 0, 0.3) 0px 10px 10px -0px;
            }
            50% {
                transform: scale(1.3);
                box-shadow: rgba(0, 0, 0, 0.3) 0px 30px 20px -0px;
            }
            100% {
                transform: scale(1);
                box-shadow: rgba(0, 0, 0, 0.3) 0px 10px 10px -0px;
            }
        }

        @keyframes color-change {
            0% {
                fill: var(--logo-color);
            }
            50% {
                fill: white;
            }
            100% {
                fill: var(--logo-color);
            }
        }
    </style>
</head>

<body>
    <!-- Loader Overlay -->
    <div class="loader-overlay" id="loaderOverlay">
        <div class="loader">
            <div class="box"></div>
            <div class="box"></div>
            <div class="box"></div>
            <div class="box"></div>
            <div class="box"></div>
            <div class="logo">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <!-- Add your logo SVG here -->
                    <path d="M12 2L2 22h20L12 2z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Login Form -->
    <div class="wrapper">
        <form action="recuperation.php" method="post" id="connexion" class="form-container front">
            <h1>Connexion Administrateur</h1>
            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            <div class="field">
                <i class="fas fa-user input-icon"></i>
                <input type="text" name="nom" id="nom" class="input-field" placeholder="Nom utilisateur" required>
            </div>

            <div class="field">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" name="pass" id="passe" class="input-field" placeholder="Mot de Passe" required>
            </div>

            <div class="btn">
                <button type="submit" class="button">
                    <span class="dots_border"></span>
                    <!-- <span class="sparkle">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path class="path" d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                            <path class="path" d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                            <path class="path" d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                        </svg>
                    </span> -->
                    <span class="text_button">Connexion</span>
                </button>
            </div>

            <div class="register-link">
                <p><a href="#" id="btn_formulaire_form">Se connecter en tant qu'étudiant</a></p>
            </div>
        </form>

        <form method="POST" action="" id="formulaire_enregistrement" class="form-container back">
            <h2>Connexion Étudiant</h2>
            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            <div class="field">
                <i class="fas fa-user input-icon"></i>
                <input type="text" name="username" class="input-field" placeholder="Nom d'utilisateur (Matricule)" required>
            </div>

            <div class="field">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" name="password" class="input-field" placeholder="Mot de passe" required>
            </div>

            <div class="btn">
                <button type="submit" class="button">
                    <span class="dots_border"></span>
                    <span class="sparkle">
                        <!-- <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path class="path" d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                            <path class="path" d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                            <path class="path" d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                        </svg> -->
                    </span>
                    <span class="text_button">Se connecter</span>
                </button>
            </div>

            <div class="register-link">
                <p><a href="#" id="btn_login_form">Se connecter en tant qu'admin</a></p>
            </div>
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

            // Show loader on form submission
            $("form").submit(function(e) {
                e.preventDefault(); // Prevent the form from submitting immediately
                $("#loaderOverlay").css("display", "flex"); // Show the loader overlay
                setTimeout(() => {
                    this.submit(); // Submit the form after a delay (simulate loading)
                }, 2000); // Adjust the delay as needed
            });
        });
    </script>
    
</body>

</html>