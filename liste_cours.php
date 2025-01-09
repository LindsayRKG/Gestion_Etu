<?php
include_once 'Classes/Database.php';
include_once 'Classes/Cours.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connexion à la base de données
$database = new Database();
$db = $database->getConnection();

// Créer une instance de la classe Cours
$cours = new Cours($db);

// Récupérer la liste des cours
$listeCours = $cours->listerCours();
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
                        <h1 class="text-center mb-4">Liste des Cours</h1>
                        <section class="section">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Cours</h5>
                                            <button onclick="window.location.href='ajouter_cours.php';" type="button" class="btn btn-outline-primary mb-3">
                                                Nouveau Cours
                                            </button>
                                            <!-- Icône de recherche et champ de saisie -->
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


                                            <!-- Tableau des informations des étudiants -->
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped text-center">
                                                    <a href="ajouter_cours.php">Ajouter un Nouveau Cours</a>
                                                    <table class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Nom du Cours</th>
                                                                <th>Niveau</th>
                                                                <th>Crédits</th>
                                                                <th>Date de Création</th>
                                                                <th>Date de Mise à Jour</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if (count($listeCours) > 0) : ?>
                                                                <?php foreach ($listeCours as $cours) : ?>
                                                                    <tr>
                                                                        <td><?= htmlspecialchars($cours['id']); ?></td>
                                                                        <td><?= htmlspecialchars($cours['nom']); ?></td>
                                                                        <td><?= htmlspecialchars($cours['niveau']); ?></td>
                                                                        <td><?= htmlspecialchars($cours['credit']); ?></td>
                                                                        <td><?= htmlspecialchars($cours['created_at']); ?></td>
                                                                        <td><?= htmlspecialchars($cours['updated_at']); ?></td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            <?php else : ?>
                                                                <tr>
                                                                    <td colspan="6" class="text-center">Aucun cours trouvé.</td>
                                                                </tr>
                                                            <?php endif; ?>
                                                        </tbody>
                                                    </table>
                                            </div>
                                        </div>
                                        <script>
                                            function searchTable() {
                                                var input, filter, table, tr, td, i, j, txtValue;
                                                input = document.getElementById("searchInput");
                                                filter = input.value.toUpperCase(); // Convertir l'entrée en majuscule
                                                table = document.querySelector(".table"); // Sélectionner le tableau
                                                tr = table.getElementsByTagName("tr"); // Récupérer toutes les lignes du tableau

                                                // Parcours de toutes les lignes du tableau, en commençant à 1 pour ignorer les en-têtes
                                                for (i = 1; i < tr.length; i++) {
                                                    td = tr[i].getElementsByTagName("td"); // Récupérer toutes les cellules de la ligne
                                                    var matchFound = false; // Flag pour savoir si on trouve une correspondance

                                                    // Parcours des cellules de la ligne
                                                    for (j = 0; j < td.length; j++) {
                                                        if (td[j]) {
                                                            txtValue = td[j].textContent || td[j].innerText; // Récupérer le texte de la cellule
                                                            if (txtValue.toUpperCase().indexOf(filter) > -1) { // Si une correspondance est trouvée
                                                                matchFound = true;
                                                                break; // Pas besoin de continuer à vérifier d'autres cellules
                                                            }
                                                        }
                                                    }

                                                    // Afficher ou cacher la ligne en fonction de la correspondance trouvée
                                                    tr[i].style.display = matchFound ? "" : "none";
                                                }
                                            }
                                        </script>


                                        <!-- Footer -->
                                        <footer id="footer" class="footer mt-4">
                                            <div class="copyright text-center">
                                                &copy; Copyright <strong><span>Keyce</span></strong>. All Rights Reserved
                                            </div>
                                            <div class="credits text-center">
                                                Designed by Groupe 7
                                            </div>
                                        </footer>


                                        <script src="script.js"></script>

</body>

</html>