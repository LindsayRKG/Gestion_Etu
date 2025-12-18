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



    public $id;
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
    public $password_changed;


    public function login($matricule, $password) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE matricule = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(1, $matricule, PDO::PARAM_STR);
        $stmt->execute();
    
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($result) {
            // Afficher les données récupérées pour déboguer
            echo "<pre>Données récupérées de la base : " . print_r($result, true) . "</pre>";
    
            // Comparer le mot de passe
            if ($password === $result['pass']) {
                // Assigner les données à l'objet
                $this->id = $result['id'];
                $this->matricule = $result['matricule'];
                $this->password_changed = $result['password_changed'] ?? 0;
                return true; // Connexion réussie
            } else {
                echo "Mot de passe incorrect.";
            }
        } else {
            echo "Aucun utilisateur trouvé avec ce matricule.";
        }
    
        return false; // Échec de la connexion
    }



// Méthode pour mettre à jour le mot de passe
public function updatePassword() {
    try {
        $query = "UPDATE " . $this->table_name . " SET pass = :new_pass WHERE matricule = :matricule";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":matricule", $this->matricule);
        $stmt->bindParam(":new_pass", $this->pass);

        return $stmt->execute();
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return false;
    }
}

// Récupérer les informations de l'étudiant par son matricule
public function getStudentByMatricule() {
    $stmt = $this->conn->prepare("SELECT * FROM ". $this->table_name . " WHERE matricule = :matricule");
    $stmt->bindParam(':matricule', $this->matricule);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
public function getStudentById() {
    $query = "SELECT * FROM etudiants WHERE id = :id LIMIT 1"; // Remplacez `matricule` par `id`
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $this->id); // Utilisez l'ID au lieu du matricule
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    return false;
}



    // Récupérer les versements de l'étudiant
    public function getVersements() {
        // Requête SQL pour récupérer les versements de l'étudiant en utilisant 'id'
        $stmt = $this->conn->prepare("SELECT * FROM versements WHERE id = :id");
        $stmt->bindParam(':id', $this->id); // L'ID de l'étudiant
        $stmt->execute();
        
        // Retourne les résultats sous forme de tableau associatif
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getNotes() {
        $query = "
            SELECT n.valeur, c.nom AS cours, n.type_note 
            FROM notes n
            JOIN cours c ON n.cours_id = c.id
            WHERE n.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        
        $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Vérification des données récupérées
        // if (empty($notes)) {
        //     echo "Aucune note trouvée pour cet étudiant.";
        // } else {
        //     echo "Notes trouvées: " . count($notes);  // Affiche le nombre de notes récupérées
        // }
    
        return $notes;
    }


    // Récupérer le bulletin de l'étudiant avec son rang et GPA
    public function getBulletin() {
        $query = "SELECT * FROM bulletins WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['id' => $this->id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Récupérer l'image de l'étudiant (photo de profil)
    public function getPhoto() {
        return $this->image;
    }



    // Modifier les informations de l'étudiant
    public function updateStudentInfo($email, $solde, $nomPrt, $emailPrt) {
        $stmt = $this->conn->prepare("
            UPDATE etudiants SET email = :email, solde = :solde, nomPrt = :nomPrt, emailPrt = :emailPrt 
            WHERE matricule = :matricule
        ");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':solde', $solde);
        $stmt->bindParam(':nomPrt', $nomPrt);
        $stmt->bindParam(':emailPrt', $emailPrt);
        $stmt->bindParam(':matricule', $this->matricule);
        return $stmt->execute();
    }

    // Ajouter un versement pour l'étudiant
    public function addVersement($montant) {
        $stmt = $this->conn->prepare("INSERT INTO versements (matricule_etudiant, montant) VALUES (:matricule_etudiant, :montant)");
        $stmt->bindParam(':matricule_etudiant', $this->matricule);
        $stmt->bindParam(':montant', $montant);
        return $stmt->execute();
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
    
            $stmt1 = $this->conn->prepare($query);
    
            // Liaison des paramètres
            $stmt1->bindParam(":matricule", $this->matricule);
            $stmt1->bindParam(":nom", $this->nom);
            $stmt1->bindParam(":prenom", $this->prenom);
            $stmt1->bindParam(":dateNaiss", $this->dateNaiss);
            $stmt1->bindParam(":Niveau", $this->niveau);
            $stmt1->bindParam(":Email", $this->email);
            $stmt1->bindParam(":statut", $this->statut);
            $stmt1->bindParam(":dateIns", $this->dateIns);
            $stmt1->bindParam(":nomPrt", $this->nomPrt);
            $stmt1->bindParam(":emailPrt", $this->emailPrt);
            $stmt1->bindParam(":pass", $this->matricule);
            $stmt1->bindParam(":solde", $this->solde);
            $stmt1->bindParam(":total", $this->total);
            $stmt1->bindParam(":image", $this->image);
            $stmt1->execute();
    
            // Insérer les informations de connexion dans la table `user`
            $query_user = "INSERT INTO user (nom, pass, password_changed) VALUES (:nom, :pass, 0)";
            $stmt2 = $this->conn->prepare($query_user);
            $stmt2->bindValue(':nom', $this->matricule, PDO::PARAM_STR);
            $stmt2->bindValue(':pass', password_hash($this->matricule, PASSWORD_BCRYPT), PDO::PARAM_STR);
            $stmt2->execute();
    
            return true;
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
            $sql = "DELETE FROM bulletins WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
    
            // Récupérer l'ID de l'étudiant à partir du matricule
            $id = $this->getEtudiantId();
            $stmt->bindParam(':id', $id);
    
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
        $pdf = new FPDF('L', 'mm', array(100, 70)); // Format carte (taille approximative)
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 10);
    
        // Ajout d'un fond bleu clair sur toute la carte
        $pdf->SetFillColor(240, 248, 255); // Couleur bleu clair
        $pdf->Rect(0, 0, 100, 70, 'F'); // Rectangle plein pour l'arrière-plan
    
        // Ajout du logo ou des informations fixes
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetTextColor(0, 0, 128); // Couleur texte (bleu foncé)
        $pdf->Image('images/logoK.png', 5, 5, 15); // Exemple avec un chemin relatif valide
    
        $pdf->SetXY(20, 5);
        $pdf->Cell(60, 5, 'MINISTERE DE L\' ENSEIGNEMENT SUPERIEURS ', 0, 1, 'L');
        $pdf->SetX(20);
        $pdf->Cell(60, 5, 'DU CAMEROUN', 0, 1, 'L');
    
        // Titre principal
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 0, 0); // Noir
        $pdf->SetXY(5, 15);
        $pdf->Cell(90, 7, mb_convert_encoding('Carte d\'étudiant De Keyce', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
    
        // Informations de l'étudiant
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetXY(10, 25);
        $pdf->Cell(30, 5, 'Nom :', 0, 0, 'R');
        $pdf->Cell(55, 5, mb_convert_encoding($this->nom, 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
    
        $pdf->SetX(10);
        $pdf->Cell(30, 5, 'Prenom :', 0, 0, 'R');
        $pdf->Cell(55, 5, mb_convert_encoding($this->prenom, 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
    
        $pdf->SetX(10);
        $pdf->Cell(30, 5, mb_convert_encoding('Né(e) le :', 'ISO-8859-1', 'UTF-8'), 0, 0, 'R');
        $pdf->Cell(55, 5, mb_convert_encoding($this->dateNaiss, 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
    
        
    
        // Ajout de la photo de l'étudiant
        $pdf->Image($this->image, 70, 25, 25, 25); // Position de la photo
    
        // Note en bas de la carte
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->SetXY(5, 50);
        // Génération du fichier PDF
        $outputPath = 'cartes_etudiants/' . $this->matricule . '_carte.pdf';
        $pdf->Output('F', $outputPath);
    
        return $outputPath;
    }
    
    
// Récupération des classes
function getClasses($db) {
    $sql = "SELECT DISTINCT Niveau FROM etudiants";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
