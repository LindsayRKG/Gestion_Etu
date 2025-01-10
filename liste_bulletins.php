<?php
// Connexion à la base de données
require_once 'Classes/Database.php';
require_once 'Classes/Etudiant.php';
require_once 'Classes/Bulletin.php';

// Connexion à la base de données
$database = new Database();
$db = $database->getConnection();

// Requête pour récupérer tous les étudiants
$query = "SELECT id, nom, prenom FROM etudiants";
$stmt = $db->prepare($query);
$stmt->execute();
$etudiants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Liste des étudiants avec bulletins</title>
</head>

<body>
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
                        <li class="breadcrumb-item active">Generer les Bulletins</li>
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
                                                <button onclick="window.location.href='generer_bulletins.php';" type="button" class="btn btn-outline-primary mb-3">
                                                    Afficher les bulletins
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
                                                
                                                <h1>Liste des étudiants avec bulletins</h1>
                                                
                                                <ul>
                                                    <?php foreach ($etudiants as $etudiant): ?>
                                                        <?php

                                                        
                                                        // Créer un objet Bulletin pour chaque étudiant
                                                        $bulletin = new Bulletin($db, $etudiant['id']);

                                                        // Vérifier si le bulletin existe
                                                        $bulletin_file = $bulletin->getBulletin();
                                                        if ($bulletin_file): ?>
                                                            <li>
                                                                <a href="afficher_bulletin.php?etudiant_id=<?= $etudiant['id']; ?>">
                                                                    <?= $etudiant['nom'] . ' ' . $etudiant['prenom']; ?>
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </ul>
    </body>

    </html>