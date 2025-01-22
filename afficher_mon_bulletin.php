<?php
ob_start();
session_start();

include_once 'Classes/Database.php';
include_once 'Classes/Etudiant.php';
include_once 'Classes/Versement.php';
include_once 'Classes/Bulletin.php';

require_once __DIR__ . '/lib/fpdf.php';

require_once __DIR__ . '/lib/FPDI-master/src/autoload.php';

// Vérifiez si l'ID étudiant est transmis
if (!isset($_GET['student_id'])) {
    ob_end_clean();
    die("Erreur : L'ID de l'étudiant est manquant.");
}

$student_id = $_GET['student_id'];

$database = new Database();
$db = $database->getConnection();

$bulletin = new Bulletin($db, $student_id);

// Récupérer les informations de l'étudiant
$etudiant = $bulletin->getInformationsEtudiant();
if (!$etudiant) {
    ob_end_clean();
    die("Erreur : Informations de l'étudiant introuvables.");
}

// Récupérer les notes de l'étudiant
$cours_data = $bulletin->getCoursParNiveau();
if (!$cours_data) {
    ob_end_clean();
    die("Erreur : Informations des cours introuvables.");
}

// Calculer les moyennes et mentions
$resultats = $bulletin->calculerMoyenneEtMention($cours_data);

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

// Personnaliser le PDF
class CustomPDF extends FPDF {
    // Ajouter un fond gris clair à toutes les pages
    public function Header() {
        $this->SetFillColor(240, 240, 240); // Gris clair
        $this->Rect(0, 0, 210, 297, 'F'); // Fond de la page
    }
}

$pdf = new CustomPDF();
$pdf->AddPage();

// Définir les styles et les couleurs
$headerColor = [50, 100, 150]; // Bleu nuit plus doux
$rowEvenColor = [230, 230, 230]; // Gris clair pour les lignes
$rowOddColor = [255, 255, 255]; // Blanc pour les lignes
$textColor = [0, 0, 0]; // Noir
$mentionColor = [255, 200, 150]; // Orange clair pour la mention
$moyenneColor = [173, 216, 230]; // Bleu clair pour la moyenne
$adminColor = [129, 183, 247]; // Bleu très clair pour l'administration
$infoColor = [220, 220, 220]; // Gris clair pour les informations de l'étudiant
$remarkTitleColor = [255, 240, 200]; // Jaune très clair pour le titre des remarques
$remarkContentColor = [255, 230, 150]; // Jaune clair pour le contenu des remarques

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
// Définir les en-têtes HTTP
header('Content-Type: application/pdf');
header('Cache-Control: no-cache, must-revalidate');
header('Content-Disposition: inline; filename="bulletin.pdf"');

// Envoyer le PDF
ob_end_clean(); // Assurez-vous que le tampon est vide
$pdf->Output('bulletin_etudiant_' . $etudiant['nom'] . '_' . $etudiant['prenom'] . '.pdf', 'I');
?>
