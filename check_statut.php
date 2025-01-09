<?php
require_once 'Classes/Database.php';
require_once 'Classes/Versement.php';

if (isset($_GET['matricule'])) {
    $matricule = $_GET['matricule'];

    $database = new Database();
    $conn = $database->getConnection();
    $manager = new VersementManager($conn);

    $statut = $manager->getStatut($matricule);
    echo json_encode(['statut' => $statut]);
}
