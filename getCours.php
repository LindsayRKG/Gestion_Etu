<?php
require_once 'Classes/Database.php';
require_once 'Classes/Notes.php';

if (isset($_GET['niveau'])) {
    $niveau = $_GET['niveau'];
    $database = new Database();
    $db = $database->getConnection();

    $noteManager = new Notes($db);
    $cours = $noteManager->getCoursParNiveau($niveau);

    echo json_encode($cours);
} else {
    echo json_encode([]);
}
?>
