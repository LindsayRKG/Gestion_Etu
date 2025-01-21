
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once 'Classes/Database.php';
require_once 'Classes/Etudiant.php';
require_once 'Classes/Versement.php';
// Connexion à la base de données


// Initialisation
$database = new Database();
$conn = $database->getConnection();
$manager = new VersementManager($conn);

// Variables
$matricules = $manager->getMatricules();
$matricule = '';
$totalAVerser = 0;
$resteAVerser = 0;
$matriculeVersement = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricule = $_POST['matricule'] ?? '';
    $montantVerse = (int)($_POST['montantVerse'] ?? 0);

    // Vérifier le statut de l'étudiant
    $statut = $manager->getStatut($matricule);

    if ($statut === 'Solvable') {
        $error = "Cet étudiant est déjà solvable et ne peut plus effectuer de versements.";
    } elseif (empty($matricule) || $montantVerse <= 0) {
        $error = "Veuillez entrer un matricule valide et un montant supérieur à 0.";
    } else {
        $totalAVerser = $manager->getTotalAVerser($matricule);
        $totalVersements = $manager->getTotalVersements($matricule);
        $resteAVerser = $totalAVerser - ($totalVersements + $montantVerse);
        $matriculeVersement = $manager->generateMatriculeVersement($matricule);

        // Insertion du versement
        $manager->insertVersement($matricule, $montantVerse, $matriculeVersement);

        // Mettre à jour le total versé et le statut
        $totalVersementsApres = $totalVersements + $montantVerse;
        $manager->updateMontantTotalVerse($matricule, $totalVersementsApres);
        $manager->updateStatut($matricule, $resteAVerser);

        // Générer le reçu PDF
        $etudiant = $manager->getEtudiantDetails($matricule); // Récupérer nom, prénom, classe
        $dateVersement = date('d-m-Y');
        $cheminRecu = $manager->genererRecuPDF(
            $matricule,
            $etudiant['nom'],
            $etudiant['prenom'],
            $etudiant['Niveau'],
            $montantVerse,
            $resteAVerser,
            $dateVersement
        );

        // Vérifier si le fichier a bien été créé avant d'afficher le message
        if (file_exists($cheminRecu)) {
            $success = "Versement enregistré avec succès. <a href='$cheminRecu' target='_blank'>Télécharger le reçu</a>.";
        } else {
            $error = "Le versement a été enregistré, mais le reçu n'a pas pu être généré.";
        }

        
    }
}


?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de Versement</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style1.css">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 600px;
        }

        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .btn-primary {
            background: #685cfe;
            border: none;
        }
    </style>
</head>

<body>




    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <div class="pagetitle">

        <nav>
            <h1>Gestion des Étudiants</h1>
            <nav>
                <ol class="breadcrumb justify-content-center">
                    <li class="breadcrumb-item"><a href="dashadmin.php">Gestion des Etudiants</a></li>
                    <li class="breadcrumb-item active">Ajouter un Versement</li>
                </ol>
            </nav>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-12">
    
                <!-- Afficher les messages de succès ou d'erreur -->
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?= $success; ?></div>
                <?php elseif (!empty($error)): ?>
                    <div class="alert alert-danger"><?= $error; ?></div>
                <?php endif; ?>
                <div class="card">
                    <h1 class="text-center mb-4">Ajouter un Versement</h1>

                    <form method="POST" action="versform.php">
                        <div class="form-group">
                            <label for="matricule">Sélectionnez un étudiant</label>
                            <select class="form-control" id="matricule" name="matricule" required>
                                <option value="">-- Choisir un étudiant --</option>
                                <?php foreach ($matricules as $etudiant): ?>
                                    <option value="<?= htmlspecialchars($etudiant['matricule']); ?>" <?= $matricule === $etudiant['matricule'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($etudiant['matricule']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="montantVerse">Montant du Versement</label>
                            <input type="number" class="form-control" id="montantVerse" name="montantVerse" required>
                        </div>

                        <div class="form-group">
                            <label for="matriculeVersement">Matricule du Versement</label>
                            <input type="text" class="form-control" id="matriculeVersement" value="<?= htmlspecialchars($matriculeVersement); ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label for="totalAVerser">Total à Verser</label>
                            <input type="text" class="form-control" id="totalAVerser" value="<?= htmlspecialchars($totalAVerser); ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label for="resteAVerser">Reste à Verser</label>
                            <input type="text" class="form-control" id="resteAVerser" value="<?= htmlspecialchars($resteAVerser); ?>" readonly>
                        </div>




                        <button type="submit" class="btn btn-primary btn-block">Effectuer le Versement</button>
                    </form>
                </div>
            </div>
        </div>

        <script>
            document.getElementById('matricule').addEventListener('change', function() {
                const matricule = this.value;

                fetch(`check_statut.php?matricule=${matricule}`)
                    .then(response => response.json())
                    .then(data => {
                        const bouton = document.querySelector('button[type="submit"]');
                        const statutField = document.getElementById('statut');

                        if (data.statut === 'Solvable') {
                            bouton.disabled = true;
                            statutField.value = 'Solvable';
                        } else {
                            bouton.disabled = false;
                            statutField.value = data.statut || 'En cours';
                        }
                    })
                    .catch(err => console.error(err));
            });
        </script>

</body>



</html>
