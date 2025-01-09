<?php
// Connexion à la base de données
require_once 'Classes/Database.php';
require_once 'Classes/Etudiant.php';
require_once 'Classes/Bulletin.php';

// Connexion à la base de données
$database = new Database();
$db = $database->getConnection();



// Afficher la liste des étudiants avec leurs bulletins
?>
<!DOCTYPE html>
<html>
<head>
    <title>Liste des étudiants avec bulletins</title>
</head>
<body>
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
