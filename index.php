

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
    body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .wrapper {
            width: 100%;
            max-width: 400px;
            position: relative;
            perspective: 1000px;
        }

        .form-container {
            width: 100%;
            position: absolute;
            transition: transform 0.8s ease-in-out;
            backface-visibility: hidden;
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

        form {
            background: #fff;
            padding: 63px;
            border-radius: 37px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        h1 {
            font-size: 24px;
            color: #333;
            text-align: center;
            margin-bottom: 49px;
        }

        .input-box {
            position: relative;
            margin-bottom: 20px;
        }

        .input-box input {
            width: 100%;
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
            border-radius: 38px;
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
    </style>
</head>
</head>
<body>
    <div class="wrapper">
        <form action="recuperation.php" method="post" id="connexion">
            <h1>Connexion Administrateur</h1>
            <div class="input-box">
                <input type="text" name="nom" id="nom" placeholder="Nom utilisateur" required>
                <i class="bx bxs-user"></i>
            </div>

            <div class="input-box">
                <input type="password" name="pass" id="passe" placeholder="Mot de Passe" required>
                <i class="bx bxs-lock-alt"></i>
            </div>

            <div class="register-link">
                <p><a href="#" id="btn_formulaire_form">Se connecter en tant que étudiant </a></p>
            </div>
            <button type="submit" class="btn">Connexion</button>

           
        </form>
   


    
        <form action="connectetd.php" method="post" id="formulaire_enregistrement">
            <h1>Connexion Etudiant</h1>
            <div class="input-box">
                <input type="text" name="matricule" id="matricule" placeholder="Nom utilisateur" required>
                <i class="bx bxs-user"></i>
            </div>

            <div class="input-box">
                <input type="password" name="pass" id="pass" placeholder="Mot de Passe" required>
                <i class="bx bxs-lock-alt"></i>
            </div>

           
            <button type="submit" name="submit" class="btn">Connexion</button>

            <div class="register-link">
                <p><a href="#" id="btn_login_form" >Se conneter en tant que admin</a></p>
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
        });
    </script>
</body>

<script src="plugins/jquery.min.js"></script>

<script type="text/javascript">
$(document).ready(function(e){
$("#formulaire_enregistrement").hide();
$("#btn_formulaire_form").click(function(){
$("#connexion").hide();
$("#formulaire_enregistrement").show();
});

$("#btn_login_form").click(function(){
$("#connexion").show();
$("#formulaire_enregistrement").hide();
});
});

</script>

</html>