<?php
include_once 'Classes/Database.php';
include_once 'Classes/Etudiant.php';


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['matricule'])) {
    $matricule = $_GET['matricule'];

    $database = new Database();
    $db = $database->getConnection();
    $etudiant = new Etudiant($db);

    // Charger les informations de l'étudiant
    $etudiant->matricule = $matricule;
    $stmt = $etudiant->getListeEtudiants();
    $etudiantData = null;

    foreach ($stmt as $row) {
        if ($row['matricule'] == $matricule) {
            $etudiantData = $row;
            break;
        }
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Mise à jour des données
        $etudiant->nom = $_POST['nom'];
        $etudiant->prenom = $_POST['prenom'];
        $etudiant->email = $_POST['email'];
        $etudiant->niveau = $_POST['niveau'];
        $etudiant->dateNaiss = $_POST['dateNaiss'];
        $etudiant->nomPrt = $_POST['nomPrt'];
        $etudiant->emailPrt = $_POST['emailPrt'];
        
        // Modifier l'étudiant
        if ($etudiant->modifierEtudiant()) {
            echo "L'étudiant a été modifié avec succès.";
        } else {
            echo "Erreur lors de la modification de l'étudiant.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un étudiant</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 800px;
        }

        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .pagetitle h1 {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .form-container {
            margin-top: 4px;
            margin-bottom: 50px;
            width: 500px !important;
            margin: 50px auto !important;
        }

        .form-container .card {
            padding: 10px;
        }

        .btn-primary {
            background: #685cfe;
            border: none;
            transition: background 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #685cfe;
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
                    <li class="breadcrumb-item active">Modifier un étudiant</li>
                </ol>
            </nav>
    </div>
    <div  id="mainContent">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <h1 class="text-center mb-4">Modifier un étudiant</h1>
                
                <form method="post" >
                    <input type="hidden" name="matricule" value="<?= $etudiantData['matricule'] ?>"> <!-- ID caché -->

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom</label>
                                <input type="text" id="nom" name="nom" class="form-control" value="<?= htmlspecialchars($etudiantData['nom']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="prenom" class="form-label">Prénom</label>
                                <input type="text" id="prenom" name="prenom" class="form-control" value="<?= htmlspecialchars($etudiantData['prenom']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="date_naissance" class="form-label">Date de naissance</label>
                                <input type="date" id="date_naissance" name="dateNaiss" class="form-control" value="<?= htmlspecialchars($etudiantData['dateNaiss']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="niveau" class="form-label">Niveau</label>
                                <select id="niveau" name="niveau" class="form-control" required>
                                    <option value="B1" <?= $etudiantData['Niveau'] == 'B1' ? 'selected' : '' ?>>B1</option>
                                    <option value="B2" <?= $etudiantData['Niveau'] == 'B2' ? 'selected' : '' ?>>B2</option>
                                    <option value="B3" <?= $etudiantData['Niveau'] == 'B3' ? 'selected' : '' ?>>B3</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($etudiantData['Email']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="nomparent" class="form-label">Nom du parent</label>
                                <input type="text" id="nomparent" name="nomPrt" class="form-control" value="<?= htmlspecialchars($etudiantData['nomPrt']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="emailparent" class="form-label">Email du parent</label>
                                <input type="email" id="emailparent" name="emailPrt" class="form-control" value="<?= htmlspecialchars($etudiantData['emailPrt']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="photo" class="form-label">Photo</label>
                                <input type="file" id="photo" name="image" class="form-control" accept="image/*">
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" name="modifier" class="btn btn-primary w-50">Modifier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>

<script src="script.js"></script>

</html>
