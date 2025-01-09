<?php
include_once 'Classes/Database.php';
include_once 'Classes/Cours.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données
    $nom = htmlspecialchars(strip_tags($_POST['nom']));
    $niveau = htmlspecialchars(strip_tags($_POST['niveau']));
    $credit = intval($_POST['credit']);

    // Valider les données
    if (empty($nom) || empty($niveau) || empty($credit)) {
        echo "Tous les champs sont obligatoires.";
        exit;
    }

    // Connexion à la base de données
    $database = new Database();
    $db = $database->getConnection();

    // Créer une instance de la classe Cours
    $cours = new Cours($db);

    // Ajouter le cours
    $data = [
        ':nom' => $nom,
        ':niveau' => $niveau,
        ':credit' => $credit,
    ];

    if ($cours->ajouterCours($data)) {
        header("Location: liste_cours.php");
        exit;
    } else {
        echo "Erreur lors de l'ajout du cours.";
    }
}
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Nouveau Cours</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 800px;
        }

        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .form-container {
            margin: 50px auto;
            width: 500px;
        }

        .form-container .card {
            padding: 20px;
        }

        .btn-primary {
            background: #685cfe;
            border: none;
            transition: background 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #5038e0;
        }
    </style>
</head>

<body>
    <div class="container form-container">
        <div class="card">
            <h1 class="text-center mb-4">Ajouter un Nouveau Cours</h1>
            <form action="ajouter_cours.php" method="post">
                <div class="form-group">
                    <label for="nom">Nom du Cours :</label>
                    <input type="text" id="nom" name="nom" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="niveau">Niveau :</label>
                    <select id="niveau" name="niveau" class="form-control" required>
                        <option value="">-- Sélectionnez un Niveau --</option>
                        <?php
                        // Récupération des niveaux depuis la table étudiants
                        $database = new Database();
                        $db = $database->getConnection();

                        $query = "SELECT DISTINCT Niveau FROM etudiants";
                        $stmt = $db->query($query);

                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='{$row['Niveau']}'>{$row['Niveau']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="credit">Crédits :</label>
                    <input type="number" id="credit" name="credit" class="form-control" min="1" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Ajouter le Cours</button>
            </form>
        </div>
    </div>
</body>

<footer class="footer text-center mt-4">
    <div class="copyright">
        &copy; Copyright <strong><span>Keyce</span></strong>. Tous droits réservés.<br>
        Designed by Groupe 7
    </div>
</footer>

</html>
