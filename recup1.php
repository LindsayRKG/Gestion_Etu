<?php
include "connexion.php";
// // Exemple d'utilisation
// $database = new Database();
//     $db = $database->getConnection();
   
    class Etudiant {
        private $conn;
    
        public function __construct($db) {
            $this->conn = $db;
        }
    
        public function genererMatricule($Niveau) {
            // Obtenir l'année en cours
            $annee = date('Y');
            $anneeSuffix = substr($annee, -2);
    
            // Compter les enregistrements existants
            $sql = "SELECT COUNT(*) AS total FROM etudiants";
            $stmt = $this->conn->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $compteur = intval($result['total']) + 1;
    
            // Générer le compteur au format 000
            $compteurFormate = str_pad($compteur, 3, '0', STR_PAD_LEFT);
    
            // Concaténation des parties du matricule
            return $anneeSuffix . strtoupper($Niveau) . $compteurFormate;
        }
    
        public function enregistrer($data) {
            $data['matricule'] = $this->genererMatricule($data['Niveau']);
            $data['Statut'] = "Insolvable";
            $data['dateIns'] = date('Y-m-d');
    
            // Définir le total selon le niveau
            switch ($data['Niveau']) {
                case "B2":
                    $data['total'] = 2000000;
                    break;
                case "B1":
                    $data['total'] = 1000000;
                    break;
                default:
                    $data['total'] = 3000000;
                    break;
            }
    
            $data['solde'] = $data['total']; // Initialiser le solde au total
                
                    // Gestion de l'image
            if (!empty($data['image']['tmp_name'])) {
                // Ouvrir l'image et la lire en tant que contenu binaire
                $imageData = file_get_contents($data['image']['tmp_name']);
                $data['image'] = $imageData; // Stocker l'image en binaire
            } else {
                $data['image'] = null;
            }
                
            // Requête d'insertion dans la base de données
            $sql = "INSERT INTO etudiants (
                matricule, nom, prenom, dateNaiss, Niveau, Email, Statut, dateIns, nomPrt, emailPrt, pass, solde, total, image
            ) VALUES (
                :matricule, :nom, :prenom, :dateNaiss, :Niveau, :Email, :Statut, :dateIns, :nomPrt, :emailPrt, :pass, :solde, :total, :image
            )";
    
            $stmt = $this->conn->prepare($sql);
    
            $stmt->bindParam(':matricule', $data['matricule']);
            $stmt->bindParam(':nom', $data['nom']);
            $stmt->bindParam(':prenom', $data['prenom']);
            $stmt->bindParam(':dateNaiss', $data['dateNaiss']);
            $stmt->bindParam(':Niveau', $data['Niveau']);
            $stmt->bindParam(':Email', $data['Email']);
            $stmt->bindParam(':Statut', $data['Statut']);
            $stmt->bindParam(':dateIns', $data['dateIns']);
            $stmt->bindParam(':nomPrt', $data['nomPrt']);
            $stmt->bindParam(':emailPrt', $data['emailPrt']);
            $stmt->bindParam(':pass', $data['matricule']);
            $stmt->bindParam(':solde', $data['solde']);
            $stmt->bindParam(':total', $data['total']);
            $stmt->bindParam(':image', $data['image'], PDO::PARAM_LOB); // Assurez-vous que le paramètre est traité comme un LOB
    
            try {
                $stmt->execute();
                return true;
            } catch (PDOException $e) {
                echo "Erreur : " . $e->getMessage();
                return false;
            }
        }
    }
    
    
    // Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = [
        'nom' => $_POST['nom'],
        'prenom' => $_POST['prenom'],
        'dateNaiss' => $_POST['dateNaiss'],
        'Niveau' => $_POST['Niveau'],
        'Email' => $_POST['Email'],
        'nomPrt' => $_POST['nomPrt'],
        'emailPrt' => $_POST['emailPrt'],
        'image' => $_FILES['image'], // Inclure l'image
    ];

    // Connexion à la base de données
    $database = new Database();
    $db = $database->getConnection();

    // Enregistrement de l'étudiant
    $etudiant = new Etudiant($db);
    if ($etudiant->enregistrer($data)) {
        header("location: index.php");
    } else {
        echo "Erreur lors de l'enregistrement.";
    }
}

?>