<?php
include "connexion.php";

// Connexion à la base de données
$database = new Database();
$db = $database->getConnection();

// Initialisation des variables pour remplir le formulaire


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Application de Gestion des Étudiants</title>
    <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: NiceAdmin
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Updated: Apr 20 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="etd.php" class="logo d-flex align-items-center">
        <img src="assets/img/logo.png" alt="">
        <span class="d-none d-lg-block">php</span>
      </a>
      
    </div><!-- End Logo -->

    

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item d-block d-lg-none">
          <a class="nav-link nav-icon search-bar-toggle " href="vers.php">
            <i class="bi bi-house-fill"></i>
          </a>
        </li><!-- End Search Icon-->

       
         
        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
           
            <span class="d-none d-md-block dropdown-toggle ps-2">Groupe 7</span>
          </a><!-- End Profile Iamge Icon -->

          
          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
 
  <main id="main" class="main">

    <div class="pagetitle">
      <h1>GestEtd</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="etd.php">Gestetd</a></li>
          <li class="breadcrumb-item active">Versement / Enregistrement</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

              <div class="card">
              
                <div class="card-body">
                <h5 class="card-title">Enregistrer un versement</h5>
        <form name="versementForm" action="recup2.php" method="post">
            <!-- Sélection du matricule -->
            <div class="mb-3">
                <label for="matricule" class="form-label">Matricule :</label>
                <select name="matrietd" id="matrietd" class="form-select" >
             
                    <option value="">-- Sélectionnez un étudiant --</option>
                    <?php
                    // Récupérer la concaténation des champs depuis la base de données
                    $sql = "SELECT matricule, CONCAT(matricule, ' - ', nom, ' ', prenom) AS display_value FROM etudiants WHERE solde>0";
                    $stmt = $db->prepare($sql);
                    $stmt->execute();
                    $etudiants = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($etudiants as $option) {
                        $selected = ($matricule == $option['matricule']) ? "selected" : "";
                        echo '<option value="' . htmlspecialchars($option['matricule']) . '" ' . $selected . '>' . htmlspecialchars($option['display_value']) . '</option>';
                        if (empty($etudiants)) {
                          echo '<option value="">Aucun étudiant disponible</option>';
                      }
                      
                      }
                    ?>
                </select>
            </div>
           
            <!-- Informations de l'étudiant -->
           
            
            <!-- Champ de montant à saisir -->
            <div class="mb-3">
                <label for="montant" class="form-label">Montant :</label>
                <input type="number" name="montant" id="montant" class="form-control" required>
            </div>
           

            <!-- Bouton d'enregistrement -->
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </form>
        </div>
    
        </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>Keyce</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
      <!-- All the links in the footer should remain intact. -->
      <!-- You can delete the links only if you purchased the pro version. -->
      <!-- Licensing information: https://bootstrapmade.com/license/ -->
      <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
      Designed by Groupe 9
    </div>
  </footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>
    <script>
        // Fonction pour mettre à jour le solde affiché
        function mettreAJourSolde() {
            const selectEtudiant = document.getElementById("matricule");
            const montantInput = document.getElementById("montant");
            const soldeInfo = document.getElementById("solde");
            const solde = selectEtudiant.options[selectEtudiant.selectedIndex].getAttribute("data-solde");

            if (solde) {
                soldeInfo.textContent = "Solde actuel : " + solde + " F CFA";
                montantInput.setAttribute("max", solde); // Définit la limite maximale
            } else {
                soldeInfo.textContent = "";
                montantInput.removeAttribute("max");
            }
        }

        // Vérification avant la soumission
        document.getElementById("versementForm").addEventListener("submit", function (event) {
            const montant = document.getElementById("montant").value;
            const solde = document.getElementById("matricule").options[document.getElementById("matricule").selectedIndex].getAttribute("data-solde");

            if (parseFloat(montant) > parseFloat(solde)) {
                event.preventDefault();
                alert("Le montant saisi dépasse le solde disponible. Veuillez saisir un montant valide.");
                document.getElementById("montant").value = ""; // Réinitialise le champ
            }
        });
    </script>
    <!-- JS Bootstrap -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
