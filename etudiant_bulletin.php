<?php
// Vérifier si l'utilisateur est connecté
session_start();
// Inclure les fichiers nécessaires
include_once 'Classes/Database.php';
include_once 'Classes/Etudiant.php';
include_once 'Classes/Versement.php';
include_once 'Classes/Bulletin.php';

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
$bulletin = new Bulletin($db, $student_id);

// Récupérer les informations de l'étudiant depuis la base de données
$student_id = $_SESSION['student_id'];
$etudiant = new Etudiant($db);  // Instancier la classe Etudiant
$etudiant->id = $student_id; // Utiliser `id` au lieu de `matricule`
$bulletin = $etudiant->getBulletin(); // Utiliser l'objet $etudiant pour appeler la méthode getBulletin()
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Bulletin de l'étudiant</title>
  <link rel="stylesheet" href="assets/css/style2.css">
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
        <img src="images/logoK.ico" alt="Logo" />
    </div>
    <h2>Bulletin</h2>
    <table>
      <thead>
        <tr>
          <th>Cours</th>
          <th>Note</th>
          <th>Moyenne</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($bulletin) {
          foreach ($bulletin['courses'] as $course) {
            echo "<tr>
                    <td>" . $course['cours'] . "</td>
                    <td>" . $course['note'] . "</td>
                    <td>" . $course['moyenne'] . "</td>
                  </tr>";
          }
        } else {
          echo "<tr><td colspan='3'>Aucun bulletin disponible</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>

  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>

</html>
