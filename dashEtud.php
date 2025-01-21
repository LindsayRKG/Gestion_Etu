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
    header("Location: index.php");
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

// // Vérifier si les informations de l'étudiant ont été récupérées
// if ($student_info) {
//     echo "<h1>Bienvenue, {$student_info['prenom']}</h1>";
//     echo "<p><strong>Matricule:</strong> {$student_info['matricule']}</p>";
//     echo "<p><strong>Nom:</strong> {$student_info['nom']}</p>";
//     echo "<p><strong>Prénom:</strong> {$student_info['prenom']}</p>";
//     echo "<p><strong>Email:</strong> {$student_info['Email']}</p>";
//     echo "<p><strong>Statut:</strong> {$student_info['Statut']}</p>";
//     echo "<p><strong>Solde:</strong> {$student_info['solde']} FCFA</p>";
//     echo "<img src=\"{$student_info['image']}\" alt=\"Carte d'étudiant\" />";
// } else {
//     echo "<p>Informations de l'étudiant non trouvées.</p>";
// }





?>

<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard de l'etudiant</title>
  <link rel="stylesheet" href="assets/css/style.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    
    
    <!-- <li><a href="etudiant_bulletin.php"><ion-icon name="document-outline"></ion-icon> Bulletin</a></li> -->

      <li>
        <a href="logout.php"><ion-icon name="log-out-outline"></ion-icon>
          <p>Logout</p>
        </a>
      </li>
    
</ul>
</div>

    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard de l'étudiant</title>
    <link rel="stylesheet" href="assets/css/style.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="assets/css/style2.css">
</head>

<body>



<div class="sidebar">
    <div class="profile">
        <ion-icon name="person-outline" role="img" class="md hydrated"></ion-icon>
    </div>
    <ul>
        <li><a href="etudiant_profil.php"><ion-icon name="person-outline"></ion-icon> Profil</a></li>
        <!-- <li><a href="etudiant_versements.php"><ion-icon name="cash-outline"></ion-icon> Versements</a></li> -->
        <li><a href="etudiant_notes.php"><ion-icon name="school-outline"></ion-icon> Notes</a></li>
        <li><a href="afficher_mon_bulletin.php?student_id=<?php echo $student_id; ?>" target="_blank"><ion-icon name="document-outline"></ion-icon> Afficher le bulletin</a></li>
        <!-- Bouton de déconnexion -->
        <li><a href="logout.php"><ion-icon name="log-out-outline"></ion-icon> Déconnexion</a></li>
    </ul>
</div>
  
    <!-- Contenu principal du tableau de bord -->
    <div class="main-content">
    <div class="logo-corner">
        <img src="images/logoK2.png" alt="Logo" />
    </div>
   
        <h1>Bienvenue, <?php echo $student_info['prenom']; ?></h1>

        <div class="student-info">
            <h2>Informations de l'étudiant</h2>
            <img src="<?php echo $student_info['image']; ?>" alt="Photo de l'étudiant" />
            <p><strong>Matricule:</strong> <?php echo $student_info['matricule']; ?></p>
            <p><strong>Nom:</strong> <?php echo $student_info['nom']; ?></p>
            <p><strong>Prénom:</strong> <?php echo $student_info['prenom']; ?></p>
            <p><strong>Email:</strong> <?php echo $student_info['Email']; ?></p>
            <p><strong>Statut:</strong> <?php echo $student_info['Statut']; ?></p>
            <p><strong>Solde:</strong> <?php echo $student_info['solde']; ?> FCFA</p>
        </div>
    </div>

    <footer class="footer">
        <div class="copyright">
            &copy; Copyright <strong><span>Keyce</span></strong>. All Rights Reserved
        </div>
        <div class="credits">
            Designed by Groupe 7
        </div>
    </footer>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>

</html>