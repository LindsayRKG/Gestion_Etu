<?php
require_once 'Classes/Database.php';
require_once 'Classes/Etudiant.php';
require_once 'Classes/Notes.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$database = new Database();
$db = $database->getConnection();

$etudiantManager = new Etudiant($db);
$etudiants = $etudiantManager->obtenirTous();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $etudiant_id = $_POST['etudiant_id'];
    $cours_id = $_POST['cours_id'];
    $type_note = $_POST['type_note'];
    $valeur = $_POST['valeur'];
    $annee_scolaire = $_POST['annee_scolaire'];
    $niveau_etudiant = $_POST['niveau_etudiant'];

   // Récupérer le niveau de l'étudiant
   $query_etudiant = "SELECT Niveau FROM etudiants WHERE id = :etudiant_id";
   $stmt_etudiant = $db->prepare($query_etudiant);
   $stmt_etudiant->execute(['etudiant_id' => $etudiant_id]);
   $niveau_etudiant = $stmt_etudiant->fetchColumn();

   // Récupérer le niveau du cours sélectionné
   $query_cours = "SELECT niveau FROM cours WHERE id = :cours_id";
   $stmt_cours = $db->prepare($query_cours);
   $stmt_cours->execute(['cours_id' => $cours_id]);
   $niveau_cours = $stmt_cours->fetchColumn();

   // Vérifier si les niveaux de l'étudiant et du cours sont identiques
   if (trim($niveau_etudiant) !== trim($niveau_cours)) {
       $message = "Erreur : L'étudiant et le cours doivent être du même niveau !";
   } else {
       // Vérifier si une note existe déjà pour l'étudiant, le cours et le type d'évaluation
       $query_existing_note = "SELECT id FROM notes WHERE etudiant_id = :etudiant_id AND cours_id = :cours_id AND type_note = :type_note";
       $stmt_existing_note = $db->prepare($query_existing_note);
       $stmt_existing_note->execute(['etudiant_id' => $etudiant_id, 'cours_id' => $cours_id, 'type_note' => $type_note]);
       $existing_note = $stmt_existing_note->fetchColumn();

       if ($existing_note) {
           // Si la note existe déjà, mettre à jour la note
           $query_update_note = "UPDATE notes SET valeur = :valeur, annee_scolaire = :annee_scolaire WHERE id = :id";
           $stmt_update_note = $db->prepare($query_update_note);
           $stmt_update_note->execute(['valeur' => $valeur, 'annee_scolaire' => $annee_scolaire, 'id' => $existing_note]);
           $message = "Note mise à jour avec succès !";
       } else {
           // Si la note n'existe pas, ajouter une nouvelle note
           $noteManager = new Notes($db);
           if ($noteManager->ajouterNote($etudiant_id, $cours_id, $type_note, $valeur, $annee_scolaire)) {
               $message = "Note ajoutée avec succès !";
           } else {
               $message = "Erreur lors de l'ajout de la note.";
           }
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
                <h1 class="mt-5">Ajouter une Note</h1>
                <?php if (isset($message)) : ?>
                    <div class="alert alert-info"><?= $message ?></div>
                <?php endif; ?>
                <form action="ajouter_notes.php" method="post">
                    <div class="form-group">
                        <label for="etudiant_id">Étudiant :</label>
                        <select id="etudiant_id" name="etudiant_id" class="form-control" required>
                            <option value="">-- Sélectionnez un étudiant --</option>
                            <?php foreach ($etudiants as $etudiant) : ?>
                                <option value="<?= $etudiant['id'] ?>" data-niveau="<?= $etudiant['Niveau'] ?>">
                                    <?= $etudiant['nom_complet'] ?> (<?= $etudiant['Niveau'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <input type="hidden" id="niveau_etudiant" name="niveau_etudiant">

                    <div class="form-group">
                        <label for="cours_id">Cours :</label>
                        <select id="cours_id" name="cours_id" class="form-control" required>
                            <option value="">-- Sélectionnez un cours --</option>
                            <?php
                            $query = "SELECT id, nom, niveau FROM cours";
                            $stmt = $db->query($query);
                            while ($cours = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='{$cours['id']}' data-niveau='{$cours['niveau']}'>{$cours['nom']} ({$cours['niveau']})</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="type_note">Type d'évaluation :</label>
                        <select id="type_note" name="type_note" class="form-control" required>
                            <option value="">-- Sélectionnez un type --</option>
                            <option value="CC">CC</option>
                            <option value="TP">TP</option>
                            <option value="Exam">Exam</option>
                            <option value="Rattrapage">Rattrapage</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="valeur">Valeur de la Note :</label>
                        <input type="number" id="valeur" name="valeur" class="form-control" min="0" max="20" step="0.1" required>
                    </div>
                    <div class="form-group">
                        <label for="annee_scolaire">Année Scolaire :</label>
                        <input type="text" id="annee_scolaire" name="annee_scolaire" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </form>
            </div>

        </div>
    </div>

    <script>
        // Écouter le changement de sélection d'étudiant
       document.getElementById('etudiant_id').addEventListener('change', function () {
    const niveauEtudiant = this.options[this.selectedIndex].getAttribute('data-niveau');
    document.getElementById('niveau_etudiant').value = niveauEtudiant;
});

document.getElementById('cours_id').addEventListener('change', function () {
    const niveauCours = this.options[this.selectedIndex].getAttribute('data-niveau');
    const niveauEtudiant = document.getElementById('niveau_etudiant').value;

    // Comparer les niveaux
    if (niveauEtudiant && niveauCours && niveauEtudiant !== niveauCours) {
        alert("Erreur : L'étudiant et le cours doivent être du même niveau !");
        // Réinitialiser la sélection du cours
        this.selectedIndex = 0;
    }
});

    </script>
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