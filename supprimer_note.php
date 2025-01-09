<?php
require_once 'Classes/Database.php';
require_once 'Classes/Notes.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connexion à la base de données
$database = new Database();
$db = $database->getConnection();

// Créer une instance de la classe Notes
$noteManager = new Notes($db);

// Vérifier si l'ID de la note à supprimer est passé en paramètre
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $note_id = $_GET['id']; // Récupérer l'ID depuis l'URL
    echo "ID récupéré : " . $_GET['id'];


    // Appeler la méthode pour supprimer la note
    if ($noteManager->supprimerNote($note_id)) {
        // Rediriger vers la liste des notes avec un message de succès
        header("Location: liste_notes.php?message=Note supprimée avec succès");
        exit();
    } else {
        // Message d'erreur si la suppression échoue
        echo "Erreur lors de la suppression de la note.";
    }
} else {
    // Message d'erreur si l'ID n'est pas valide
    echo "L'ID fourni n'est pas valide.";
}
?>
