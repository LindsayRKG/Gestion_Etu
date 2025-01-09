<?php
include_once 'Classes/Database.php';
include_once 'Classes/Etudiant.php';

if (isset($_GET['matricule'])) {
    $matricule = $_GET['matricule'];

    $database = new Database();
    $db = $database->getConnection();
    $etudiant = new Etudiant($db);

    // Charger les informations de l'étudiant
    $etudiant->matricule = $matricule;

    // Supprimer l'étudiant
    if ($etudiant->supprimerEtudiant()) {
        echo "L'étudiant a été supprimé avec succès.";
    } else {
        echo "Erreur lors de la suppression de l'étudiant.";
    }
}
?>
