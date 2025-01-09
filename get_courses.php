<?php
if (isset($_GET['etudiant_id'])) {
    $etudiant_id = intval($_GET['etudiant_id']);
    
    // Connexion à la base de données
    include 'Classes/Database.php'; // Votre fichier de connexion
    
    // Récupérer le niveau de l'étudiant
    $sql_etudiant = "SELECT Niveau FROM etudiants WHERE id = ?";
    $stmt = $conn->prepare($sql_etudiant);
    $stmt->bind_param("i", $etudiant_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $niveau = $row['niveau'];
        
        // Récupérer les cours correspondants
        $sql_cours = "SELECT id, nom FROM cours WHERE niveau = ?";
        $stmt_cours = $conn->prepare($sql_cours);
        $stmt_cours->bind_param("s", $niveau);
        $stmt_cours->execute();
        $result_cours = $stmt_cours->get_result();
        
        $cours = [];
        while ($row_cours = $result_cours->fetch_assoc()) {
            $cours[] = $row_cours;
        }
        
        // Retourner les cours sous forme JSON
        echo json_encode($cours);
    } else {
        echo json_encode([]);
    }
    
    $stmt->close();
    $conn->close();
}
?>
