<?php
// Vérifier si l'utilisateur est connecté
session_start();
// Inclure les fichiers nécessaires
include_once 'Classes/Database.php';
include_once 'Classes/Etudiant.php';
include_once 'Classes/Versement.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Connexion à la base de données
$database = new Database();
$db = $database->getConnection();

if (!isset($_SESSION['student_id'])) {
    header("Location: loginEtudiant.php");
    exit;
}
//echo $_SESSION['student_id']; // Ajoutez ceci pour vérifier si la session contient le matricule correct

// Récupérer les informations de l'étudiant depuis la base de données
$student_id = $_SESSION['student_id'];
$etudiant = new Etudiant($db);  // Instancier la classe Etudiant
$etudiant->id = $student_id; // Utiliser `id` au lieu de `matricule`
$student_info = $etudiant->getStudentById();

// Récupérer les notes et les versements
$notes = $etudiant->getNotes();
$versements = $etudiant->getVersements();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Notes de l'étudiant</title>
    <link rel="stylesheet" href="assets/css/style2.css" />
</head>

<body>
    <div class="sidebar">
        <div class="profile">
            <ion-icon name="person-outline" role="img" class="md hydrated"></ion-icon>
        </div>
        <ul>
            <li><a href="etudiant_profil.php"><ion-icon name="person-outline"></ion-icon> Profil</a></li>
            <li><a href="etudiant_versements.php"><ion-icon name="cash-outline"></ion-icon> Versements</a></li>
            <li><a href="etudiant_notes.php"><ion-icon name="school-outline"></ion-icon> Notes</a></li>
            <li><a href="afficher_mon_bulletin.php?student_id=<?php echo $student_id; ?>" target="_blank"><ion-icon name="document-outline"></ion-icon> Afficher le bulletin</a></li>
        </ul>
    </div>

    <div class="main-content">
    <div class="logo-corner">
        <img src="images/logoK2.png" alt="Logo" />
    </div>
    <h1> Consultez vos notes </h1>
        <h2>Notes</h2>
        <table>
            <thead>
                <tr>
                    <th>Cours</th>
                    <th>Type de Note</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>

                <?php foreach ($notes as $note) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($note['cours']); ?></td>
                        <td><?php echo htmlspecialchars($note['type_note']); ?></td>
                        <td><?php echo htmlspecialchars($note['valeur']); ?></td>
                    </tr>
                <?php } ?>
        </table>
        </tbody>
        </table>

        <footer class="footer">
        <div class="copyright">
            &copy; Copyright <strong><span>Keyce</span></strong>. All Rights Reserved
        </div>
        <div class="credits">
            Designed by Groupe 7
        </div>
    </footer>

    </div>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>

</html>