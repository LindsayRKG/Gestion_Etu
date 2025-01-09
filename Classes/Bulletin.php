<?php
require_once('lib/TCPDF-main/tcpdf.php'); // Inclure TCPDF

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Bulletin
{
    private $db;
    private $etudiant_id;

    public function __construct($db, $etudiant_id)
    {
        $this->db = $db;
        $this->etudiant_id = $etudiant_id;
    }


    // Récupérer toutes les informations nécessaires pour le bulletin
    public function getInformationsEtudiant()
    {
        $query = "SELECT e.nom, e.prenom, e.dateNaiss, e.image, e.Niveau, e.email
                  FROM etudiants e
                  WHERE e.id = :etudiant_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['etudiant_id' => $this->etudiant_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // Récupérer les cours de l'étudiant et ses notes par type
    public function getCoursParNiveau()
    {
        $query = "SELECT c.nom, c.credit, n.type_note, n.valeur AS note 
              FROM cours c
              JOIN etudiants e ON e.niveau = c.niveau
              LEFT JOIN notes n ON n.etudiant_id = e.id AND n.cours_id = c.id
              WHERE e.id = :etudiant_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['etudiant_id' => $this->etudiant_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Récupérer les notes de l'étudiant et calculer la moyenne et mention
    public function calculerMoyenneEtMention($cours_data)
    {
        $total_points = 0;
        $total_credits = 0;
        $cours_traite = [];

        foreach ($cours_data as $cours) {
            // Vérifier si le cours a déjà été traité pour éviter de compter plusieurs fois les crédits
            if (!in_array($cours['nom'], $cours_traite)) {
                // Ajouter le nom du cours dans le tableau pour éviter de le traiter à nouveau
                $cours_traite[] = $cours['nom'];

                // Initialiser les notes des différents types
                $cc_note = 0;
                $tp_note = 0;
                $exam_note = 0;
                $rattrapage_note = 0;

                // Organiser les notes par type pour chaque cours
                foreach ($cours_data as $cours_inner) {
                    if ($cours_inner['nom'] == $cours['nom']) {
                        if ($cours_inner['type_note'] == 'CC') {
                            $cc_note = $cours_inner['note'];
                        } elseif ($cours_inner['type_note'] == 'TP') {
                            $tp_note = $cours_inner['note'];
                        } elseif ($cours_inner['type_note'] == 'Exam') {
                            $exam_note = $cours_inner['note'];
                        } elseif ($cours_inner['type_note'] == 'Rattrapage') {
                            $rattrapage_note = $cours_inner['note'];
                        }
                    }
                }

                // Calculer la moyenne pondérée du cours
                $moyenne_cours = ($cc_note * 0.2) + ($tp_note * 0.4) + (($rattrapage_note > 0) ? $rattrapage_note : $exam_note) * 0.4;

                // Calculer les points totaux pour ce cours (moyenne * crédit du cours)
                $total_points += $moyenne_cours * $cours['credit'];
                // Ajouter une seule fois le crédit du cours
                $total_credits += $cours['credit'];
            }
        }

        // Calculer la moyenne générale
        $moyenne_generale = ($total_credits > 0) ? $total_points / $total_credits : 0;

        // Calculer la mention en fonction de la moyenne générale
        $mention = $this->getMention($moyenne_generale);

        return ['moyenne' => $moyenne_generale, 'mention' => $mention, 'total_points' => $total_points];
    }





    // Déterminer la mention en fonction de la moyenne
    private function getMention($moyenne)
    {
        if ($moyenne >= 16) {
            return 'Excellent';
        } elseif ($moyenne >= 14) {
            return 'Bien';
        } elseif ($moyenne >= 12) {
            return 'Assez Bien';
        } else {
            return 'Passable';
        }
    }


    // Générer le PDF du bulletin
    public function genererBulletinPDF()
    {
        // Informations de l'étudiant
        $etudiant = $this->getInformationsEtudiant();
        $cours_data = $this->getCoursParNiveau();
        $resultats = $this->calculerMoyenneEtMention($cours_data);

        // Vérification si le bulletin existe déjà pour cet étudiant
        $query = "SELECT * FROM bulletins WHERE etudiant_id = :etudiant_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['etudiant_id' => $this->etudiant_id]);
        $existing_bulletin = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si le bulletin existe, on le met à jour avec les nouvelles informations
        if ($existing_bulletin) {
            $query = "UPDATE bulletins SET moyenne = :moyenne, mention = :mention, date_creation = NOW() 
                      WHERE etudiant_id = :etudiant_id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'moyenne' => $resultats['moyenne'],
                'mention' => $resultats['mention'],
                'etudiant_id' => $this->etudiant_id
            ]);
        } else {
            // Si le bulletin n'existe pas, on l'insère
            $query = "INSERT INTO bulletins (etudiant_id, moyenne, mention, date_creation) 
                      VALUES (:etudiant_id, :moyenne, :mention, NOW())";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'etudiant_id' => $this->etudiant_id,
                'moyenne' => $resultats['moyenne'],
                'mention' => $resultats['mention']
            ]);
        }

        // Création du PDF
        $pdf = new TCPDF();
        $pdf->AddPage();

        // Titre
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'Bulletin de l\'etudiant: ' . $etudiant['nom'] . ' ' . $etudiant['prenom'], 0, 1, 'C');

        // Photo
        $photo_path = 'uploads/' . $etudiant['image']; // Assurez-vous que le chemin d'accès est correct
        if (file_exists($photo_path)) {
            $pdf->Image($photo_path, 10, 30, 30, 30);
        }

        // Informations personnelles
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, 'Nom: ' . $etudiant['nom'] . ' ' . $etudiant['prenom'], 0, 1);
        $pdf->Cell(0, 10, 'Date de Naissance: ' . $etudiant['dateNaiss'], 0, 1);
        $pdf->Cell(0, 10, 'Email: ' . $etudiant['email'], 0, 1);

        // Table des cours
        $pdf->Cell(0, 10, 'Liste des cours:', 0, 1);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(80, 10, 'Cours', 1);
        $pdf->Cell(40, 10, 'Crédit', 1);
        $pdf->Cell(40, 10, 'Note', 1);
        $pdf->Ln();

        foreach ($cours_data as $cours) {
            $pdf->Cell(80, 10, $cours['nom'], 1);
            $pdf->Cell(40, 10, $cours['credit'], 1);
            $pdf->Cell(40, 10, $cours['note'] ?? '0', 1);
            $pdf->Ln();
        }

        // Résultats
        $pdf->Cell(0, 10, 'Moyenne: ' . $resultats['moyenne'], 0, 1);
        $pdf->Cell(0, 10, 'Mention: ' . $resultats['mention'], 0, 1);

        // Créer le dossier Bulletins si nécessaire
        // Créer le dossier Bulletins si nécessaire
        $bulletins_dir = __DIR__ . 'Bulletins/'; // Utilise __DIR__ pour obtenir le chemin absolu
        if (!file_exists($bulletins_dir)) {
            mkdir($bulletins_dir, 0777, true); // Crée le dossier avec des permissions d'écriture
        }

        // Enregistrer le PDF dans le dossier Bulletins
        // Enregistrer le PDF dans le dossier Bulletins
        $file_path = $bulletins_dir . 'bulletin_etudiant_' . $etudiant['nom'] . '_' . $etudiant['prenom'] . '.pdf';
        $pdf->Output($file_path, 'F'); // Sauvegarder dans le fichier au lieu de l'afficher



        // Vérifier si le fichier a été créé
        if (file_exists($file_path)) {
            echo "Bulletin PDF généré avec succès : " . $file_path;
        } else {
            echo "Erreur lors de la génération du bulletin PDF.";
        }

        // Optionnel : retourner le chemin du fichier pour un lien ou autre usage
        return $file_path;
        // Afficher un message avec le lien pour télécharger le bulletin
        echo "Le bulletin a été généré avec succès. Vous pouvez le télécharger en cliquant sur le lien suivant : <a href='$file_path' target='_blank'>Télécharger le bulletin</a>";
    }

    // Méthode pour récupérer le bulletin d'un étudiant
    public function getBulletin()
    {
        // Récupérer les informations de l'étudiant
        $etudiant = $this->getInformationsEtudiant();
    
        // Chemin du dossier des bulletins
        $bulletins_dir = __DIR__ . '/Bulletins/'; // Utilisation de __DIR__ pour obtenir le chemin absolu du dossier
    
        // Construction du nom du fichier PDF
        $bulletin_file = $bulletins_dir . 'bulletin_etudiant_' . $etudiant['nom'] . '_' . $etudiant['prenom'] . '.pdf';
    
        // Vérifier si le fichier bulletin existe
        if (file_exists($bulletin_file)) {
            return $bulletin_file; // Retourner le chemin complet si le fichier existe
        } else {
            return false; // Retourner false si le fichier n'existe pas
        }
    }
    


    // Enregistrer le bulletin dans la base de données
    // public function enregistrerBulletin()
    // {
    //     $moyenne_mention = $this->calculerMoyenneEtMention();
    //     $moyenne = $moyenne_mention['moyenne'];
    //     $mention = $moyenne_mention['mention'];

    //     $query = "INSERT INTO bulletins (etudiant_id, moyenne, mention) 
    //               VALUES (:etudiant_id, :moyenne, :mention)";
    //     $stmt = $this->db->prepare($query);
    //     return $stmt->execute([
    //         'etudiant_id' => $this->etudiant_id,
    //         'moyenne' => $moyenne,
    //         'mention' => $mention
    //     ]);
    // }

    // // Récupérer les informations de l'étudiant
    // private function getEtudiant()
    // {
    //     $query = "SELECT * FROM etudiants WHERE id = :etudiant_id";
    //     $stmt = $this->db->prepare($query);
    //     $stmt->execute(['etudiant_id' => $this->etudiant_id]);
    //     return $stmt->fetch(PDO::FETCH_ASSOC);
    // }
}
