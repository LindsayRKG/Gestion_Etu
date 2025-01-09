<?php
// Inclure les fichiers nécessaires
include_once 'Classes/Database.php';
include_once 'Classes/Etudiant.php';




ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();

    $etudiant = new Etudiant($db);

    // Récupération des données du formulaire
    $etudiant->nom = $_POST['nom'];
    $etudiant->prenom = $_POST['prenom'];
    $etudiant->dateNaiss = $_POST['dateNaiss'];
    $etudiant->niveau = $_POST['Niveau'];
    $etudiant->email = $_POST['Email'];
    $etudiant->nomPrt = $_POST['nomPrt'];
    $etudiant->emailPrt = $_POST['emailPrt'];

    // Générer le matricule et définir les champs calculés
    $etudiant->matricule = $etudiant->genererMatricule($etudiant->niveau);
    $etudiant->dateIns = date('Y-m-d');
    $etudiant->statut = "Insolvable";
    $etudiant->total = $etudiant->niveau === "B1" ? 1000000 : ($etudiant->niveau === "B2" ? 2000000 : 3000000);
    $etudiant->solde = $etudiant->total;

    // Gestion de la photo
    $target_dir = "uploads/"; // Répertoire où les images seront stockées

    // Vérifiez que le répertoire existe, sinon créez-le
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    // Définir les types MIME autorisés pour les images
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $filename = basename($_FILES["image"]["name"]); // Récupère le nom de l'image
        $target_file = $target_dir . $filename; // Combine le répertoire et le nom du fichier

        // Vérification du type MIME (facultatif)
        if (!in_array($_FILES["image"]["type"], $allowed_types)) {
            die("Erreur : Seuls les formats JPEG, PNG et GIF sont autorisés.");
        }

        // Déplacez le fichier téléchargé
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            echo "Image téléchargée avec succès !";
            $etudiant->image = $target_file; // Enregistrez le chemin de l'image
        } else {
            die("Erreur : Impossible de déplacer le fichier téléchargé.");
        }
    } else {
        // Image par défaut si aucun fichier n’est téléchargé ou en cas d’erreur
        $etudiant->image = "uploads/default.jpg";
    }

    echo "Chemin du fichier cible : " . $target_file;
    echo "Chemin du fichier temporaire : " . $_FILES['image']['tmp_name'];





    // Après avoir ajouté l'étudiant dans la base de données
    if ($etudiant->ajouterEtudiant()) {
        // Générer la carte étudiant et récupérer son chemin
        $cartePath = $etudiant->genererCarteEtudiant($_POST);

        echo "L'étudiant a été ajouté avec succès. <a href='$cartePath' target='_blank'>Télécharger la carte</a>";

        $emailDestinataire = $_POST['Email'] ?? $result['email']; // Priorité au formulaire

        // Envoyer la carte par email
        // Préparer les données pour l'email
        $sujet = "Votre carte étudiant";
        $message = "
    Bonjour {$etudiant->prenom} {$etudiant->nom},<br><br>
    Votre carte étudiant a été générée avec succès. Veuillez trouver votre carte en pièce jointe.<br><br>
    Cordialement,<br>L'équipe académique.
";
        $fichierJoint = $cartePath; // Chemin de la carte PDF générée

        // Envoyer l'email
        if ($etudiant->envoyerEmail($emailDestinataire, $sujet, $message, $fichierJoint)) {
            echo "L'email a été envoyé avec succès à $emailDestinataire.";
        } else {
            echo "Erreur lors de l'envoi de l'email.";
        }
    }

    // if (isset($_POST['ajouter'])) {
    //     $ajout = $etudiant->enregistrer($_POST);

    //     if ($ajout) {
    //         $cartePath = $etudiant->genererCarteEtudiant($_POST);
    //         echo "L'étudiant a été ajouté avec succès. <a href='$cartePath' target='_blank'>Télécharger la carte</a>";
    //     } else {
    //         echo "Erreur lors de l'ajout.";
    //     }
    // }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un étudiant</title>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="/assets/css/style.css"> -->
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 800px;

            /* Largeur maximale du formulaire */
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
            /* Priorité sur les styles Bootstrap */
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
                    <li class="breadcrumb-item active">Ajouter un étudiant</li>
                </ol>
            </nav>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <h1 class="text-center mb-4">Ajouter un étudiant</h1>
                <form method="post" action="ajout_Etud.php" enctype="multipart/form-data">

                    <div class="row">
                        <!-- Première colonne -->
                        <div class="col-md-6">

                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom</label>
                                <input type="text" id="nom" name="nom" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="prenom" class="form-label">Prénom</label>
                                <input type="text" id="prenom" name="prenom" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="dateNaiss" class="form-label">Date de naissance</label>
                                <input type="date" id="date_naissance" name="dateNaiss" class="form-control" required>
                            </div>
                            <!-- <div class="mb-3">
                                <label for="sexe" class="form-label">Sexe</label><br>
                                <input type="radio" id="masculin" name="sexe" value="masculin" required>
                                <label for="masculin">Masculin</label><br>
                                <input type="radio" id="feminin" name="sexe" value="feminin" required>
                                <label for="feminin">Féminin</label>
                            </div> -->
                            <div class="mb-3">
                                <label for="niveau" class="form-label">Niveau</label>
                                <select id="niveau" name="Niveau" class="form-control" required>
                                    <option value="B1">B1</option>
                                    <option value="B2">B2</option>
                                    <option value="B3">B3</option>
                                </select>
                            </div>

                        </div>

                        <!-- Deuxième colonne -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="Email" class="form-control" required>
                            </div>

                            <!-- <div class="mb-3">
                                <label for="date_inscription" class="form-label">Date d'inscription</label>
                                <input type="date" id="date_inscription" name="dateInscription" class="form-control" required>
                            </div> -->
                            <div class="mb-3">
                                <label for="nomparent" class="form-label">Nom du parent</label>
                                <input type="text" id="nomparent" name="nomPrt" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="emailparent" class="form-label">Email du parent</label>
                                <input type="email" id="emailparent" name="emailPrt" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="photo" class="form-label">Photo</label>
                                <input type="file" id="photo" name="image" class="form-control" accept="image/*" required>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" name="ajouter" class="btn btn-primary w-50">Ajouter</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    </div>
</body>

<!-- ======= Footer ======= -->
<footer id="footer" class="footer">
    <div class="copyright">
        &copy; Copyright <strong><span>Keyce</span></strong>. All Rights Reserved
    </div>
    <div class="credits">

        Designed by Groupe 7
    </div>
</footer><!-- End Footer -->

</html>