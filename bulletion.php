<?php
include 'connexion.php';
require('fpdf/fpdf.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function showError($message) {
    echo "
    <!DOCTYPE html>
    <html lang='fr'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Erreur</title>
        <style>
            body {
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
                background: linear-gradient(135deg, #ff7e5f, #feb47b);
                font-family: Arial, sans-serif;
            }
            .error-container {
                text-align: center;
                padding: 20px;
                background: white;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                border-radius: 10px;
                animation: float 2s infinite ease-in-out;
            }
            .error-container h1 {
                font-size: 2em;
                color: #e74c3c;
                margin: 0;
            }
            .error-container p {
                font-size: 1.2em;
                color: #555;
            }
            .error-container a {
                display: inline-block;
                margin-top: 10px;
                padding: 10px 20px;
                background: #e74c3c;
                color: white;
                text-decoration: none;
                border-radius: 5px;
                transition: background 0.3s;
            }
            .error-container a:hover {
                background: #c0392b;
            }
            @keyframes float {
                0%, 100% {
                    transform: translateY(0);
                }
                50% {
                    transform: translateY(-10px);
                }
            }
        </style>
    </head>
    <body>
        <div class='error-container'>
            <h1>Erreur !</h1>
            <p>$message</p>
            <a href='javascript:history.back()'>Revenir</a>
        </div>
    </body>
    </html>";
    exit;
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    // Connexion à la base de données
    $database = new Database();
    $db = $database->getConnection();

    function getMention($moyenne) {
        if ($moyenne >= 16) {
            return "Excellent";
        } elseif ($moyenne >= 14 && $moyenne < 16) {
            return "Bien";
        } elseif ($moyenne >= 12 && $moyenne < 14) {
            return "Assez Bien";
        } elseif ($moyenne >= 10 && $moyenne < 12) {
            return "Passable";
        } else {
            return "Insuffisant";
        }
    }

    // Récupérer les informations de l'étudiant
    $sql_etudiant = "SELECT * FROM etudiants WHERE id= :id";
    $stmt_etudiant = $db->prepare($sql_etudiant);
    $stmt_etudiant->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt_etudiant->execute();
    $etudiant = $stmt_etudiant->fetch(PDO::FETCH_ASSOC);

    if (!$etudiant) {
        showError("Étudiant introuvable.");
    }

    // Définir les poids pour chaque catégorie
    $poids_categories = [
        'CC' => 10, // 10%
        'TD' => 30, // 30%
        'Exam' => 60 // 60%
    ];

    $categories_obligatoires = array_keys($poids_categories);

    // Requête pour récupérer les notes par matière et catégorie
    $sql = "
        SELECT 
            *
        FROM 
            notes
        WHERE 
            etd = :etd
        ORDER BY 
            matiere, categorie;
    ";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':etd', $id, PDO::PARAM_INT);
    $stmt->execute();
    $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Organiser les données par matière
    $data = [];
    $categories = [];
    $moyennes = [];

    foreach ($notes as $note) {
        $matiere = $note['matiere'];
        $categorie = $note['categorie'];
        $valeurNote = $note['note'];

        // Ajouter la catégorie à la liste des catégories uniques
        if (!in_array($categorie, $categories)) {
            $categories[] = $categorie;
        }

        // Organiser les données par matière
        if (!isset($data[$matiere])) {
            $data[$matiere] = [];
            $moyennes[$matiere] = ['total' => 0, 'poids' => 0];
        }

        $data[$matiere][$categorie] = $valeurNote;

        // Calculer la moyenne pondérée pour chaque matière
        if (isset($poids_categories[$categorie])) {
            $moyennes[$matiere]['total'] += $valeurNote * ($poids_categories[$categorie] / 100);
        }
    }

    // Vérifier si toutes les matières ont des notes pour toutes les catégories obligatoires
    foreach ($data as $matiere => $notesParCategorie) {
        foreach ($categories_obligatoires as $categorie) {
            if (!isset($notesParCategorie[$categorie])) {
                showError("La matière '<strong>$matiere</strong>' ne contient pas toutes les catégories de notes requises.");
            }
            else{ // Calculer la moyenne générale
                $total_moyenne = 0;
                $total_matieres = count($moyennes);
                
                foreach ($moyennes as $matiere => $moyenne) {
                    $total_moyenne += $moyenne['total'];
                }
                
                $moyenne_generale = $total_matieres > 0 ? $total_moyenne / $total_matieres : 0;
                $mention = getMention($moyenne_generale);
                
                // Trier les catégories pour l'ordre des colonnes
                sort($categories);
                
                // Générer le PDF avec FPDF
                $pdf = new FPDF();
                $pdf->AddPage();
                $pdf->SetFont('Arial', 'B', 16);
                
                // Titre
                $pdf->Cell(0, 10, 'Bulletin de Notes', 0, 1, 'C');
                $pdf->Ln(10);
                
                // Informations de l'étudiant
                $pdf->SetFont('Arial', '', 12);
                $pdf->Cell(0, 10, 'Nom : ' . $etudiant['nom'] , 0, 1);
                $pdf->Cell(0, 10, 'Prenom : ' . $etudiant['prenom'], 0, 1);
                $pdf->Cell(0, 10, 'Date de naissance : ' . $etudiant['dateNaiss'], 0, 1);
                $pdf->Cell(0, 10, 'Matricule : ' . $etudiant['matricule'], 0, 1);
                $pdf->Cell(0, 10, 'Niveau : ' . $etudiant['Niveau'], 0, 1);
                $pdf->Ln(10);
                
                // Table des notes
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->Cell(60, 10, 'Matiere', 1);
                foreach ($categories as $categorie) {
                    $pdf->Cell(40, 10, $categorie, 1);
                }
                $pdf->Cell(40, 10, 'Moyenne', 1);
                $pdf->Ln();
                
                $pdf->SetFont('Arial', '', 12);
                foreach ($data as $matiere => $notesParCategorie) {
                    $pdf->Cell(60, 10, $matiere, 1);
                    foreach ($categories as $categorie) {
                        $note = isset($notesParCategorie[$categorie]) ? $notesParCategorie[$categorie] : '-';
                        $pdf->Cell(40, 10, $note, 1);
                    }
                    $pdf->Cell(40, 10, number_format($moyennes[$matiere]['total'], 2), 1);
                    $pdf->Ln();
                }
                
                // Moyenne générale et mention
                $pdf->Ln(10);
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->Cell(0, 10, 'Moyenne Generale : ' . number_format($moyenne_generale, 2), 0, 1, 'C');
                $pdf->Ln(5);
                $pdf->Cell(0, 10, 'Mention : ' . $mention, 0, 1, 'C');
                
                // Enregistrement du PDF
                $pdf_path = 'bulletins/bulletin_' . $id . '.pdf';
                $pdf->Output('F', $pdf_path);
                
                // Envoi de l'e-mail
                $mail = new PHPMailer(true);
                
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'ondoatamar@gmail.com';
                    $mail->Password = 'cebzssqbrbnvxrfd';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;
                
                    $mail->setFrom('ondoatamar@gmail.com', 'Administration');
                    $mail->addAddress($etudiant['Email'], $etudiant['nom'] . ' ' . $etudiant['prenom']);
                    $mail->addCC($etudiant['emailPrt'], 'Parent de ' . $etudiant['nom'] . ' ' . $etudiant['prenom']);
                
                    $mail->addAttachment($pdf_path);
                
                    $mail->isHTML(true);
                    $mail->Subject = 'Bulletin de notes';
                    $mail->Body    = "Bonjour {$etudiant['prenom']} {$etudiant['nom']},<br><br>
                                      Veuillez trouver ci-joint votre bulletin de notes.<br><br>
                                      Cordialement,<br>L'administration.";
                
                    $mail->send();
                    echo 'Le bulletin a été envoyé avec succès.';
                } catch (Exception $e) {
                    echo "L'envoi de l'e-mail a échoué. Erreur : {$mail->ErrorInfo}";
                }
                
                header('Content-Type: application/pdf');
                }  // Calculs des moyennes et génération du PDF (restant inchangés)
                
                }
        }
    }

   
  