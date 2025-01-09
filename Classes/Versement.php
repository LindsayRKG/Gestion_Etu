<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'lib/fpdf.php';



class VersementManager
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getMatricules()
    {
        $sql = "SELECT id, matricule FROM etudiants";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalAVerser($matricule)
    {
        $sql = "SELECT total FROM etudiants WHERE matricule = :matricule";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':matricule', $matricule, PDO::PARAM_STR);
        $stmt->execute();
        $etudiant = $stmt->fetch(PDO::FETCH_ASSOC);
        return $etudiant ? $etudiant['total'] : 0;
    }

    public function getTotalVersements($matricule)
    {
        $sql = "SELECT IFNULL(SUM(montant), 0) AS montant_total_verse FROM versements WHERE matricule_etudiant = :matricule";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':matricule', $matricule, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['montant_total_verse'] : 0;
    }

    public function generateMatriculeVersement($matricule)
    {
        $sql = "SELECT COUNT(*) AS num_versement FROM versements WHERE matricule_etudiant = :matricule";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':matricule', $matricule, PDO::PARAM_STR);
        $stmt->execute();
        $numVersement = $stmt->fetch(PDO::FETCH_ASSOC)['num_versement'] + 1;
        return '2024-' . $matricule . '-' . str_pad($numVersement, 3, '0', STR_PAD_LEFT);
    }

    public function insertVersement($matricule, $montant, $matriculeVersement)
    {
        $sql = "INSERT INTO versements (matricule_etudiant, montant, matricule_versement) VALUES (:matricule, :montant, :matriculeVersement)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':matricule', $matricule, PDO::PARAM_STR);
        $stmt->bindParam(':montant', $montant, PDO::PARAM_INT);
        $stmt->bindParam(':matriculeVersement', $matriculeVersement, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function updateMontantTotalVerse($matricule, $totalVersementsApres)
    {
        $sql = "UPDATE etudiants SET montant_total_verse = :totalVersementsApres WHERE matricule = :matricule";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':totalVersementsApres', $totalVersementsApres, PDO::PARAM_STR);
        $stmt->bindParam(':matricule', $matricule, PDO::PARAM_STR);
        $stmt->execute();
    }


    public function getAllVersements()
    {
        $sql = "SELECT v.matricule_versement, v.matricule_etudiant, v.montant, e.nom, e.prenom
                FROM versements v
                JOIN etudiants e ON v.matricule_etudiant = e.matricule";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Méthode pour récupérer le statut d'un étudiant
    public function getStatut($matricule)
    {
        $query = "SELECT statut FROM etudiants WHERE matricule = :matricule";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':matricule', $matricule);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['statut'] : null;
    }

    // Méthode pour mettre à jour le statut de l'étudiant
    public function updateStatut($matricule, $resteAVerser)
    {
        $statut = $resteAVerser > 0 ? 'En cours' : 'Solvable';
        $query = "UPDATE etudiants SET statut = :statut WHERE matricule = :matricule";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':statut', $statut);
        $stmt->bindParam(':matricule', $matricule);
        $stmt->execute();
    }


    function genererRecuPDF($matricule, $nom, $prenom, $classe, $montantVerse, $resteAVerser, $dateVersement) {
        // Nom du dossier
        $directory = 'recus_versements';
        
        // Vérification et création du dossier si nécessaire
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }
        
        // Chemin complet du fichier PDF
        $cheminRecu = $directory . "/$matricule-recu-" . time() . ".pdf";
    
        // Création du PDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 14);
    
        // En-tête
        $pdf->Cell(0, 10, 'RECU DE VERSEMENT', 0, 1, 'C');
        $pdf->Ln(10);
    
        // Informations générales
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(50, 10, 'Matricule :', 0, 0);
        $pdf->Cell(0, 10, $matricule, 0, 1);
    
        $pdf->Cell(50, 10, 'Nom :', 0, 0);
        $pdf->Cell(0, 10, $nom . ' ' . $prenom, 0, 1);
    
        $pdf->Cell(50, 10, 'Classe :', 0, 0);
        $pdf->Cell(0, 10, $classe, 0, 1);
    
        $pdf->Cell(50, 10, 'Date du versement :', 0, 0);
        $pdf->Cell(0, 10, $dateVersement, 0, 1);
    
        $pdf->Ln(10);
    
        // Détails du paiement
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(50, 10, 'Montant Verse :', 0, 0);
        $pdf->Cell(0, 10, number_format($montantVerse, 0, ',', ' ') . ' FCFA', 0, 1);
    
        $pdf->Cell(50, 10, 'Reste à Verser :', 0, 0);
        $pdf->Cell(0, 10, number_format($resteAVerser, 0, ',', ' ') . ' FCFA', 0, 1);
    
        $pdf->Ln(20);
    
        // Signature
        $pdf->SetFont('Arial', 'I', 12);
        $pdf->Cell(0, 10, 'Signature de l\'administration', 0, 1, 'R');
    
        // // Enregistrer le fichier PDF
        // $pdf->Output('F', $cheminRecu);

         // Enregistrer le fichier PDF
    try {
        $pdf->Output('F', $cheminRecu);
    } catch (Exception $e) {
        throw new Exception("Erreur lors de la génération du reçu : " . $e->getMessage());
    }

    
        return $cheminRecu;
    }
    

    public function getEtudiantDetails($matricule) {
        $query = "SELECT nom, prenom, Niveau FROM etudiants WHERE matricule = :matricule";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':matricule', $matricule);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    

}
