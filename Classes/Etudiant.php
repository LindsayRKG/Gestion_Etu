<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../lib/fpdf.php';

require 'lib/PHPMailer-master/src/Exception.php';
require 'lib/PHPMailer-master/src/PHPMailer.php';
require 'lib/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



class Etudiant
{
    private $conn;
    private $table_name = "etudiants";

    // Constructeur pour initialiser la connexion à la base de données
    public function __construct($conn)
    {
        $this->conn = $conn;
    }



    public $matricule;
    public $nom;
    public $prenom;
    public $dateNaiss;
    public $niveau;
    public $email;
    public $statut;
    public $dateIns;
    public $nomPrt;
    public $emailPrt;
    public $pass;
    public $solde;
    public $total;
    public $image;

    public function _construct($db)
    {
        $this->conn = $db;
    }

    public function ajouterEtudiant()
    {
        try {
            $query = "INSERT INTO " . $this->table_name . " 
                      (matricule, nom, prenom, dateNaiss, Niveau, Email, statut, dateIns, 
                       nomPrt, emailPrt, pass, solde, total, image) 
                      VALUES 
                      (:matricule, :nom, :prenom, :dateNaiss, :Niveau, :Email, :statut, :dateIns, 
                       :nomPrt, :emailPrt, :pass, :solde, :total, :image)";

            $stmt = $this->conn->prepare($query);

            // Liaison des paramètres
            $stmt->bindParam(":matricule", $this->matricule);
            $stmt->bindParam(":nom", $this->nom);
            $stmt->bindParam(":prenom", $this->prenom);
            $stmt->bindParam(":dateNaiss", $this->dateNaiss);
            $stmt->bindParam(":Niveau", $this->niveau);
            $stmt->bindParam(":Email", $this->email);
            $stmt->bindParam(":statut", $this->statut);
            $stmt->bindParam(":dateIns", $this->dateIns);
            $stmt->bindParam(":nomPrt", $this->nomPrt);
            $stmt->bindParam(":emailPrt", $this->emailPrt);
            $stmt->bindParam(":pass", $this->matricule);
            $stmt->bindParam(":solde", $this->solde);
            $stmt->bindParam(":total", $this->total);
            $stmt->bindParam(":image", $this->image);

            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
            return false;
        }
    }



    // Méthode pour récupérer la liste des étudiants
    public function getListeEtudiants()
    {
        $sql = "SELECT matricule, nom, prenom, dateNaiss, Email, Niveau, nomPrt, emailPrt, Statut, dateIns, image FROM etudiants";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $etudiants = $stmt->fetchAll(PDO::FETCH_ASSOC);


        return $etudiants;
    }

public function genererMatricule($Niveau)
{
    $annee = date('Y');
    $anneeSuffix = substr($annee, -2);

    // Démarrer une transaction pour éviter les collisions
    $this->conn->beginTransaction();

    try {
        // Récupérer et incrémenter le compteur pour le niveau spécifié
        $sql = "SELECT compteur FROM matricule_counter WHERE niveau = :niveau FOR UPDATE";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':niveau' => $Niveau]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            throw new Exception("Le niveau spécifié n'existe pas.");
        }

        $compteur = intval($result['compteur']) + 1;

        // Mettre à jour le compteur dans la table
        $sqlUpdate = "UPDATE matricule_counter SET compteur = :compteur WHERE niveau = :niveau";
        $stmtUpdate = $this->conn->prepare($sqlUpdate);
        $stmtUpdate->execute([':compteur' => $compteur, ':niveau' => $Niveau]);

        // Confirmer la transaction
        $this->conn->commit();

        // Générer le matricule formaté
        $compteurFormate = str_pad($compteur, 3, '0', STR_PAD_LEFT);
        return $anneeSuffix . strtoupper($Niveau) . $compteurFormate;

    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $this->conn->rollBack();
        throw new Exception("Erreur lors de la génération du matricule : " . $e->getMessage());
    }
}



    // Modifier un étudiant
    public function modifierEtudiant()
    {
        $sql = "UPDATE " . $this->table_name . " 
                SET nom = :nom, prenom = :prenom, dateNaiss = :dateNaiss, email = :email, niveau = :niveau, 
                    nomPrt = :nomPrt, emailPrt = :emailPrt,   image = :image, 
                    total = :total, solde = :solde
                WHERE matricule = :matricule";

        $stmt = $this->conn->prepare($sql);

        // Liaison des paramètres
        $stmt->bindParam(':matricule', $this->matricule);
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':prenom', $this->prenom);
        $stmt->bindParam(':dateNaiss', $this->dateNaiss);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':niveau', $this->niveau);
        $stmt->bindParam(':nomPrt', $this->nomPrt);
        $stmt->bindParam(':emailPrt', $this->emailPrt);
        // $stmt->bindParam(':statut', $this->statut);
        //$stmt->bindParam(':dateIns', $this->dateIns);
        $stmt->bindParam(':image', $this->image);
        $stmt->bindParam(':total', $this->total);
        $stmt->bindParam(':solde', $this->solde);

        return $stmt->execute();
    }

    public function supprimerVersementsAssocies()
    {
        $query = "DELETE FROM versements WHERE matricule_etudiant = :matricule";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':matricule', $this->matricule, PDO::PARAM_STR);
        return $stmt->execute();
    }


    public function supprimerEtudiant()
    {
        try {
            // Supprimer les versements associés
            $this->supprimerVersementsAssocies();
    
            // Supprimer les bulletins associés
            $this->supprimerBulletinsAssocies();
    
            // Supprimer l'étudiant
            $sql = "DELETE FROM " . $this->table_name . " WHERE matricule = :matricule";
            $stmt = $this->conn->prepare($sql);
    
            // Liaison du paramètre
            $stmt->bindParam(':matricule', $this->matricule);
    
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Erreur lors de la suppression de l'étudiant : " . $e->getMessage();
            return false;
        }
    }
    
    /**
     * Supprimer les bulletins associés à l'étudiant.
     */
    private function supprimerBulletinsAssocies()
    {
        try {
            $sql = "DELETE FROM bulletins WHERE etudiant_id = :etudiant_id";
            $stmt = $this->conn->prepare($sql);
    
            // Récupérer l'ID de l'étudiant à partir du matricule
            $etudiant_id = $this->getEtudiantId();
            $stmt->bindParam(':etudiant_id', $etudiant_id);
    
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Erreur lors de la suppression des bulletins associés : " . $e->getMessage();
        }
    }
    
    /**
     * Récupérer l'ID de l'étudiant à partir du matricule.
     */
    private function getEtudiantId()
    {
        try {
            $sql = "SELECT id FROM " . $this->table_name . " WHERE matricule = :matricule";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':matricule', $this->matricule);
            $stmt->execute();
    
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['id'] ?? null;
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération de l'ID de l'étudiant : " . $e->getMessage();
            return null;
        }
    }
    

    function genererRecuPDF($matricule, $nom, $prenom, $classe, $montantVerse, $resteAVerser, $dateVersement)
    {
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

        // Sauvegarde ou sortie du PDF
        $filename = "recus_versements/recu_" . $matricule . ".pdf";
        if (!file_exists('recus_versements')) {
            mkdir('recus_versements', 0777, true);
        }
        $pdf->Output('F', $filename);

        return $filename;
    }

  
    public function genererCarteEtudiant()
    {
        // Vérifiez si l'image de l'étudiant existe
        if (empty($this->image) || !file_exists($this->image)) {
            throw new Exception("Erreur : aucune image fournie ou le fichier image est introuvable.");
        }
    
        // Initialisation de FPDF
        $pdf = new FPDF('L', 'mm', array(85, 54)); // Format carte (taille approximative)
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 10);
    
        // Ajout d'un fond bleu clair et des lignes graphiques
        $pdf->SetFillColor(240, 248, 255); // Couleur bleu clair
        $pdf->Rect(0, 0, 85, 54, 'F'); // Rectangle plein pour l'arrière-plan
    
        // Ajout du logo ou des informations fixes
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetTextColor(0, 0, 128); // Couleur texte (bleu foncé)
        $pdf->Image('images/logoK.png', 5, 5, 15); // Exemple avec un chemin relatif valide

    
        $pdf->SetXY(20, 5);
        $pdf->Cell(60, 5, 'MINISTERE DES ENSEIGNEMENTS SUPERIEURS ', 0, 1, 'L');
        $pdf->SetX(20);
        $pdf->Cell(60, 5, 'DU CAMEROUN', 0, 1, 'L');
    
        // Titre principal
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetTextColor(0, 0, 0); // Noir
        $pdf->SetXY(3, 20);
        $pdf->Cell(80, 7, mb_convert_encoding('Carte d\'étudiant de Keyce', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
    
        // Informations de l'étudiant
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetXY(5, 30);
        $pdf->Cell(30, 5, 'Nom :', 0, 0);
        $pdf->Cell(50, 5, mb_convert_encoding($this->nom, 'ISO-8859-1', 'UTF-8'), 0, 1);
    
        $pdf->SetX(5);
        $pdf->Cell(30, 5, 'Prenom :', 0, 0);
        $pdf->Cell(50, 5, mb_convert_encoding($this->prenom, 'ISO-8859-1', 'UTF-8'), 0, 1);
    
        $pdf->SetX(5);
        $pdf->Cell(30, 5, mb_convert_encoding('Né(e) le :', 'ISO-8859-1', 'UTF-8'), 0, 0);
        $pdf->Cell(50, 5, mb_convert_encoding($this->dateNaiss, 'ISO-8859-1', 'UTF-8'), 0, 1);
    
        $pdf->SetX(5);
        $pdf->Cell(30, 5, 'Niveau :', 0, 0);
        $pdf->Cell(50, 5, mb_convert_encoding($this->niveau, 'ISO-8859-1', 'UTF-8'), 0, 1);
    
        // Ajout de la photo de l'étudiant
        $pdf->Image($this->image, 60, 20, 20, 25); // Position de la photo
    
        // Génération du fichier PDF
        $outputPath = 'cartes_etudiants/' . $this->matricule . '_carte.pdf';
        $pdf->Output('F', $outputPath);
    
        return $outputPath;
    }

    function envoyerEmail($emailDestinataire, $sujet, $message, $fichierJoint = null) {
        $mail = new PHPMailer(true);
    
        try {
            // Configuration du serveur SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Ex. Gmail
            $mail->SMTPAuth = true;
            $mail->Username = 'lindsayrbcc@gmail.com'; // Votre email
            $mail->Password = 'yvoqhaovgcknnham'; // Mot de passe ou App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
    
            // Paramètres de l'email
            $mail->setFrom('lindsayrbcc@gmail.com', 'ADMINISTRATION');
            
            $mail->addAddress($emailDestinataire);
    
            // Ajout d'une pièce jointe
            if ($fichierJoint && file_exists($fichierJoint)) {
                $mail->addAttachment($fichierJoint);
            } else if ($fichierJoint) {
                error_log("Fichier joint introuvable : $fichierJoint");
            }
            
            $mail->isHTML(true);
            $mail->Subject = $sujet;
            $mail->Body = $message;
    
            $mail->send();
            return true;
        } catch (Exception $e) {
            echo "Erreur d'envoi d'email : {$mail->ErrorInfo}";
            error_log("Erreur d'envoi d'email : {$mail->ErrorInfo}");
            return false;
        }
        
    }

    public function obtenirTous()
    {
        $query = "SELECT id, CONCAT(nom, ' ', prenom) AS nom_complet, Niveau FROM etudiants";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getCoursParNiveau($niveau)
{
    $query = "SELECT id, nom FROM cours WHERE niveau = :niveau";
    $stmt = $this->conn->prepare($query);
    $stmt->execute(['niveau' => $niveau]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}    
