<?php
// Activer l'affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclusion des classes nécessaires
require_once 'Classes/Database.php';
require_once 'Classes/Etudiant.php';

// Connexion à la base de données
$db = new Database();
$conn = $db->getConnection();

if (!$conn) {
    die("Erreur : Impossible d'établir une connexion à la base de données.");
}

if (!class_exists('Etudiant')) {
    die("Erreur : La classe Etudiant n'est pas définie.");
}

// Instancier la classe Etudiant
$etudiant = new Etudiant($conn);
if (!$etudiant) {
    die("Erreur : Impossible de créer une instance de la classe Etudiant.");
}

// Supprimer un étudiant si un matricule est fourni
if (isset($_GET['matricule']) && !empty($_GET['matricule'])) {
    $matricule = htmlspecialchars($_GET['matricule']); // Sécurisation de l'entrée utilisateur

    // Charger le matricule de l'étudiant
    $etudiant->matricule = $matricule;

    // Supprimer l'étudiant
    if ($etudiant->supprimerEtudiant()) {
        echo "<div class='alert alert-success'>L'étudiant a été supprimé avec succès.</div>";
    } else {
        echo "<div class='alert alert-danger'>Erreur lors de la suppression de l'étudiant.</div>";
    }
}

// Récupérer la liste des étudiants
$etudiants = $etudiant->getListeEtudiants();
?>



<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Étudiants</title>
    <link rel="stylesheet" href="style1.css">

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

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

        .card {
            position: relative;
            display: flex;
            flex-direction: column;
            min-width: 0px;
            overflow-wrap: break-word;
            background-color: rgb(255, 255, 255);
            background-clip: border-box;
            border-width: 1px;
            border-style: solid;
            border-color: rgba(0, 0, 0, 0.125);
            border-image: initial;
            border-radius: 4.25rem;
        }
    </style>
</head>

<body>
    <div class="pagetitle">
        <nav>
            <h1>Gestion des Étudiants</h1>
            <nav>
                <ol class="breadcrumb justify-content-center">
                    <li class="breadcrumb-item"><a href="dashadmin.php">Gestion des Étudiants</a></li>
                    <li class="breadcrumb-item active">Liste des Étudiants</li>
                </ol>
            </nav>

            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card">
                        <h1 class="text-center mb-4">Liste des Étudiants</h1>
                        <section class="section">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Étudiants</h5>
                                            <button onclick="window.location.href='ajout_Etud.php';" type="button" class="btn btn-outline-primary mb-3">
                                                Nouvel Étudiant
                                            </button>



                                            <!-- Icone de recherche-->
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped text-center">
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
                                                    <thead class="custom-header">
                                                        <tr>
                                                            <th>Image</th>
                                                            <th>Matricule</th>
                                                            <th>Nom</th>
                                                            <th>Prénom</th>
                                                            <th>Date de Naissance</th>
                                                            <th>Email</th>
                                                            <th>Niveau</th>
                                                            <th>Nom Parent</th>
                                                            <th>Email Parent</th>
                                                            <th>Statut</th>
                                                            <th>Date d'inscription</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        // Vérifier si le tableau $etudiants contient des données
                                                        if (count($etudiants) > 0):
                                                            foreach ($etudiants as $etudiant): ?>
                                                                <tr>

                                                                    <td>
                                                                        <?php
                                                                        $imagePath = $etudiant['image'];
                                                                        // Vérification si le fichier existe
                                                                        if (file_exists($imagePath)): ?>
                                                                            <img src="<?= $imagePath ?>" alt="Photo de <?= htmlspecialchars($etudiant['nom']) ?>" width="50" height="50">
                                                                        <?php else: ?>
                                                                            <span>Image non trouvée</span>
                                                                        <?php endif; ?>
                                                                    </td>

                                                                    <td><?= htmlspecialchars($etudiant['matricule']) ?></td>
                                                                    <td><?= htmlspecialchars($etudiant['nom']) ?></td>
                                                                    <td><?= htmlspecialchars($etudiant['prenom']) ?></td>
                                                                    <td><?= htmlspecialchars($etudiant['dateNaiss']) ?></td>
                                                                    <td><?= htmlspecialchars($etudiant['Email']) ?></td>
                                                                    <td><?= htmlspecialchars($etudiant['Niveau']) ?></td>
                                                                    <td><?= htmlspecialchars($etudiant['nomPrt']) ?></td>
                                                                    <td><?= htmlspecialchars($etudiant['emailPrt']) ?></td>
                                                                    <td><?= htmlspecialchars($etudiant['Statut']) ?></td>
                                                                    <td><?= htmlspecialchars($etudiant['dateIns']) ?></td>
                                                                    <td>
                                                                        <!-- Bouton Modifier -->
                                                                        <a href="modifer_etudiant.php?matricule=<?= $etudiant['matricule']; ?>" class="btn btn-primary">Modifier</a>

                                                                        <a href="lister_et.php?matricule=<?= $etudiant['matricule']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet étudiant ?');">Supprimer</a>

                                                                    </td>

                                                                </tr>
                                                            <?php endforeach;
                                                        else: ?>
                                                            <tr>
                                                                <td colspan="10" class="text-center">Aucun étudiant trouvé.</td>
                                                            </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
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
            <script src="script.js"></script>

</body>

</html>