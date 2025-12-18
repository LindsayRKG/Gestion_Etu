<?php
require_once 'Classes/Database.php';
require_once 'Classes/Etudiant.php';
require_once 'Classes/Notes.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$database = new Database();
$db = $database->getConnection();

// Récupérer les étudiants d'une classe donnée
function getEtudiantsParClasse($db, $classe) {
    $sql = "SELECT id, nom, prenom, CONCAT(nom, ' ', prenom) AS display_value FROM etudiants WHERE Niveau = :Niveau";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':Niveau', $classe);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Vérifier si une classe a été sélectionnée
if (isset($_GET['Niveau']) && !empty($_GET['Niveau'])) {
    $classe = $_GET['Niveau'];
    $etudiants = getEtudiantsParClasse($db, $classe);
} else {
    echo "Aucune classe sélectionnée.";
    exit;
}

// Fonction pour insérer les données dans la table cible
function insertElements($db, $data, $cat,$mat) {
    $sql = "INSERT INTO notes (etudiant_id,cours_id,type_note,valeur) VALUES (:etudiant_id, :cours_id,:type_note, :valeur)";
    $stmt = $db->prepare($sql);
    foreach ($data as $row) {
        $stmt->bindParam(':etudiant_id', $row['etudiant_id']);
        $stmt->bindParam(':valeur', $row['valeur']);
        $stmt->bindParam(':cours_id', $mat);
        $stmt->bindParam(':type_cours', $cat);
        $stmt->execute();
    }
}

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cat = $_POST['type_cours'];
    $mat = $_POST['cours_id'];
    $data = [];
    foreach ($_POST['notes'] as $id => $note) {
        $data[] = [
            'etudiant_id' => $id,
            'valeur' => $note,
        ];
    }
    insertElements($db, $data, $cat,$mat);
    header("Location: etd.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un étudiant</title>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style1.css">
    
 
</head>

<body>
    <div class="pagetitle">

        <nav>
            <h1>Gestion des Étudiants</h1>
            <nav>
                <ol class="breadcrumb justify-content-center">
                    <li class="breadcrumb-item"><a href="dashadmin.php">Gestion des Étudiants</a></li>
                    <li class="breadcrumb-item active">Ajouter une Note</li>
                </ol>
            </nav>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <h1 class="mt-5">Ajouter une Note</h1>
                <h2>Étudiants de la classe : <?php echo htmlspecialchars($classe); ?></h2>
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
                    <button type="submit" class="btn btn-primary">Ajouter la Note</button>
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


</html>