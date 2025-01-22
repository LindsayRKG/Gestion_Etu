<?php
namespace App;
use PDO;
use PDOException;
use FPDF;


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../lib/fpdf.php';
require_once __DIR__ . '/../lib/FPDI-master/src/autoload.php';

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

        // Générer un hash du contenu pour la signature numérique
        $bulletinContent = "Nom: " . $etudiant['nom'] . " " . $etudiant['prenom'] . "\n";
        $bulletinContent .= "Date de Naissance: " . $etudiant['dateNaiss'] . "\n";
        $bulletinContent .= "Niveau: " . $etudiant['Niveau'] . "\n";

        // Ajouter les notes des cours au contenu
        foreach ($cours_data as $cours) {
            $bulletinContent .= "Cours: " . $cours['nom'] . " - Note: " . $cours['note'] . "\n";
        }

        // Générer le hash SHA-256
        $hash = hash('sha256', $bulletinContent);

        // Création du PDF
        $pdf = new FPDF();
        $pdf->AddPage();

        // Définir les styles et les couleurs
        $headerColor = [50, 100, 150]; // Bleu nuit plus doux
        $rowEvenColor = [230, 230, 230]; // Gris clair pour les lignes
        $rowOddColor = [255, 255, 255]; // Blanc pour les lignes
        $textColor = [0, 0, 0]; // Noir
        $mentionColor = [255, 200, 150]; // Orange clair pour la mention
        $moyenneColor = [173, 216, 230]; // Bleu clair pour la moyenne
        $adminColor = [220, 230, 240]; // Bleu très clair pour l'administration
        $infoColor = [220, 220, 220]; // Gris clair pour les informations de l'étudiant
        $remarkTitleColor = [240, 240, 220]; // Gris très clair pour le titre des remarques
        $remarkContentColor = [250, 250, 230]; // Gris très clair pour le contenu des remarques

        // Augmenter la taille du logo
        $pdf->Image('images/logoK.png', 5, 10, 20);

        // Ajouter les informations générales
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->SetTextColor(...$headerColor);
        $pdf->Cell(0, 15, "Bulletin Scolaire", 0, 1, 'C', false);

        $pdf->SetFont('Arial', '', 12);
        $pdf->SetTextColor(...$textColor);
        $pdf->Ln(10);

        // Vérifiez si l'image de l'étudiant existe
        $imagePath = '' . $etudiant['image'];

        if (empty($etudiant['image']) || !file_exists($imagePath)) {
            $pdf->SetFont('Arial', 'I', 10);
            $pdf->Cell(0, 10, '(Photo non disponible)', 0, 1, 'R');
        } else {
            // Si l'image est disponible, l'afficher
            $pdf->Image($imagePath, 150, 10, 40, 40, '', '', '', true);
        }

        // Informations de l'étudiant avec couleur de fond
        $pdf->SetFillColor(...$infoColor);
        $pdf->Rect(10, $pdf->GetY(), 130, 30, 'F'); // Rectangle pour les informations de l'étudiant
        $pdf->SetXY(10, $pdf->GetY());
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Nom: ' . $etudiant['nom'] . ' ' . $etudiant['prenom'], 0, 1);
        $pdf->Cell(0, 10, 'Date de Naissance: ' . $etudiant['dateNaiss'], 0, 1);
        $pdf->Cell(0, 10, 'Niveau: ' . $etudiant['Niveau'], 0, 1);
        $pdf->Ln(10);

        // Titre du tableau des notes
        $pdf->SetFillColor(...$headerColor);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(80, 10, 'Cours', 0, 0, 'C', true);
        $pdf->Cell(40, 10, 'Credit', 0, 0, 'C', true);
        $pdf->Cell(40, 10, 'Note', 0, 1, 'C', true);

        // Ajouter les données des notes
        $pdf->SetFont('Arial', '', 12);
        $lineToggle = false;
        foreach ($cours_data as $cours) {
            $fillColor = $lineToggle ? $rowEvenColor : $rowOddColor;
            $pdf->SetFillColor(...$fillColor);
            $pdf->SetTextColor(...$textColor);
            $pdf->Cell(80, 10, $cours['nom'], 0, 0, 'C', true);
            $pdf->Cell(40, 10, $cours['credit'], 0, 0, 'C', true);
            $pdf->Cell(40, 10, $cours['note'], 0, 1, 'C', true);
            $lineToggle = !$lineToggle;
        }

        // Ajouter la moyenne générale avec encadrement et fond de couleur
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(...$textColor);

        // Coordonnées pour le rectangle de la moyenne générale
        $x = 10;
        $y = $pdf->GetY();
        $width = 90;
        $height = 10;

        // Dessiner le rectangle avec fond de couleur pour la moyenne générale
        $pdf->SetFillColor(...$moyenneColor); // Couleur de fond pour la moyenne générale
        $pdf->Rect($x, $y, $width, $height, 'F');

        // Ajouter la moyenne générale à l'intérieur du rectangle
        $pdf->SetXY($x + 5, $y);
        $pdf->Cell(0, 10, 'Moyenne Generale: ' . $resultats['moyenne'], 0, 1);

        // Ajouter la mention avec encadrement et fond de couleur
        $pdf->Ln(5);

        // Coordonnées pour le rectangle de la mention
        $x = 10;
        $y = $pdf->GetY();
        $width = 90;
        $height = 10;

        // Dessiner le rectangle avec fond de couleur pour la mention
        $pdf->SetFillColor(...$mentionColor); // Couleur de fond pour la mention
        $pdf->Rect($x, $y, $width, $height, 'F');

        // Ajouter la mention à l'intérieur du rectangle
        $pdf->SetXY($x + 5, $y);
        $pdf->Cell(0, 10, 'Mention: ' . $resultats['mention'], 0, 1);

        // Ajouter la zone des remarques avec couleur de fond
        $pdf->Ln(10);
        $pdf->SetFillColor(...$remarkTitleColor); // Couleur de fond pour le titre des remarques
        $pdf->Rect(10, $pdf->GetY(), 190, 10, 'F'); // Rectangle pour le titre des remarques
        $pdf->SetXY(10, $pdf->GetY());
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Remarques:', 0, 1);

        $pdf->SetFillColor(...$remarkContentColor); // Couleur de fond pour le contenu des remarques
        $pdf->Rect(10, $pdf->GetY(), 190, 30, 'F'); // Rectangle pour le contenu des remarques
        $pdf->SetXY(10, $pdf->GetY());
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->MultiCell(0, 20, 'Continuez a travailler dur pour atteindre vos objectifs.', 0, 'L', false);


        // Ajouter une ligne vide pour l'espacement
        $pdf->Ln(5);

        // Ajouter la section d'administration avec couleur de fond
        $pdf->SetFillColor(...$adminColor); // Couleur de fond pour l'administration
        $pdf->Rect(10, $pdf->GetY(), 190, 60, 'F'); // Rectangle pour la section d'administration

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 10, 'Administration:', 0, 1, 'L');

        // Ajouter un message ou une signature d'administration
        $pdf->SetFont('Arial', '', 10);
        $pdf->MultiCell(0, 10, "Ce bulletin a ete generer et valide par l'administration. Pour toute question, veuillez contacter l'administration à l'adresse suivante : keycyde@gmail.com.", 0, 'L', false);

        // Optionnel : ajouter une signature de l'administrateur si nécessaire
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->Cell(0, 10, 'Signature de l\'administration: ______________________', 0, 5, 'L');

        // Ajouter l'image de la signature de l'administration après cette section
        $yPosition = $pdf->GetY() - 20; // Ajustez la position Y en soustrayant une valeur pour monter l'image
        $pdf->Image('images/signature-Photoroom.png', 60, $yPosition, 30, 20);


        // Ajouter le hash sous la signature
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 10, 'Hash numerique: ' . $hash, 0, 1, 'C');
        // Créer le dossier Bulletins si nécessaire
        $bulletins_dir = __DIR__ . '/Bulletins/'; // Utilise __DIR__ pour obtenir le chemin absolu
        if (!file_exists($bulletins_dir)) {
            mkdir($bulletins_dir, 0777, true); // Crée le dossier avec des permissions d'écriture
        }

        // Chemin du fichier PDF
        $file_path = $bulletins_dir . 'bulletin_etudiant_' . $etudiant['nom'] . '_' . $etudiant['prenom'] . '.pdf';

        // Vérifier si le fichier existe déjà
        if (file_exists($file_path)) {
            // Supprimer le fichier existant
            unlink($file_path);
        }

        // Enregistrer le PDF dans le dossier Bulletins
        $pdf->Output($file_path, 'F'); // Sauvegarder dans le fichier au lieu de l'afficher

        // Vérifier si le fichier a été créé
        if (file_exists($file_path)) {
            echo "Bulletin PDF généré avec succès : " . $file_path;
        } else {
            echo "Erreur lors de la génération du bulletin PDF.";
        }

        // Optionnel : retourner le chemin du fichier pour un lien ou autre usage
        return $file_path;
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
