<?php
// Connexion à la base de données
include_once 'Classes/Database.php'; // Veuillez adapter ce fichier à votre base de données
include_once 'Classes/Bulletin.php'; // Veuillez adapter ce fichier à votre base de données

$database = new Database();
$db = $database->getConnection();
// Récupération des étudiants
$query = "SELECT * FROM etudiants";
$stmt = $db->query($query);
$etudiants = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['etudiant_id'])) {
    $etudiant_id = $_GET['etudiant_id'];

    // Création de l'objet Bulletin
    $bulletin = new Bulletin($db, $etudiant_id);

    // Générer le bulletin en PDF
    $bulletin->genererBulletinPdf();

    // Enregistrer le bulletin dans la base de données
    // $bulletin->enregistrerBulletin();
}
?>





<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Versements</title>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Lien vers Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 20px;
        }

        .table-container {
            margin-top: 20px;
        }

        .btn-primary,
        .btn-danger {
            margin-right: 5px;
        }

        .btn-primary:hover {
            background-color: #4b50b5;
        }

        .btn-danger:hover {
            background-color: #d9534f;
        }

        .custom-header {
            background-color: #4CAF50;
            /* Couleur de fond verte */
            color: white;
            /* Couleur du texte blanche */
        }
    </style>
</head>

<body>
    <div class="pagetitle">
        <nav>
            <h1>Gestion des Cours</h1>
            <nav>
                <ol class="breadcrumb justify-content-center">
                    <li class="breadcrumb-item"><a href="dashadmin.php">Gestion des Étudiants</a></li>
                    <li class="breadcrumb-item active">Liste des Cours</li>
                </ol>
            </nav>

            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card">
                        <h1 class="text-center mb-4">Generer les Bulletins</h1>
                        <section class="section">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Cours</h5>
                                            <button onclick="window.location.href='ajouter_cours.php';" type="button" class="btn btn-outline-primary mb-3">
                                                Afficher
                                            </button>
                                            <!-- Icône de recherche et champ de saisie -->
                                            
                                            <div class="row mb-3">
                                                <div class="col-md-4 offset-md-8">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="searchInput" placeholder="Rechercher..." onkeyup="searchTable()">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">
                                                                <i class="fa fa-search"></i> <!-- Icône de recherche -->
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <h2>Liste des étudiants</h2>
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Nom</th>
                                                        <th>Prénom</th>
                                                        <th>Niveau</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($etudiants as $etudiant): ?>
                                                        <tr>
                                                            <td><?= $etudiant['id'] ?></td>
                                                            <td><?= $etudiant['nom'] ?></td>
                                                            <td><?= $etudiant['prenom'] ?></td>
                                                            <td><?= $etudiant['Niveau'] ?></td>
                                                            <td>
                                                                <!-- Le bouton pour générer le bulletin -->
                                                                <a href="?etudiant_id=<?= $etudiant['id'] ?>">Générer et enregistrer le bulletin</a>
                                                            </td>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </nav>
    </div>
    

                

</body>

</html>