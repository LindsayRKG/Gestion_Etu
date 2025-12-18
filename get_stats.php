<?php
header('Content-Type: application/json');

// Connexion à la base de données
$host = 'localhost'; // Remplacez par vos informations
$dbname = 'basededonnes'; // Remplacez par le nom de votre base
$username = 'root'; // Remplacez par votre utilisateur
$password = 'Callita4'; // Remplacez par votre mot de passe

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}



// Récupération du type de statistique demandée
$statType = $_GET['stat'] ?? '';

switch ($statType) {
    case 'solvabilite':
        // Solvabilité : Montant payé et restant par étudiant
        $query = "
    
SELECT 
    e.matricule, 
    e.nom, 
    e.prenom, 
    e.total AS montant_total,
    e.solde AS solde_restant,
    SUM(v.montant) AS montant_paye,
    CASE
        -- L'étudiant est solvable si le montant payé est égal ou supérieur au montant total
        WHEN SUM(v.montant) >= e.total THEN 'Solvable'
        -- L'étudiant est en cours si le montant payé est inférieur au montant total
        WHEN SUM(v.montant) < e.total AND SUM(v.montant) > 0 THEN 'En Cours'
        -- L'étudiant est insolvable si le montant payé est nul
        WHEN SUM(v.montant) = 0 THEN 'Insolvable'
        -- Par défaut, l'étudiant est insolvable
        ELSE 'Insolvable'
    END AS categorie
FROM 
    etudiants e
LEFT JOIN 
    versements v ON e.matricule = v.matricule_etudiant
GROUP BY 
    e.matricule;
    ";
    
        break;

    case 'etudiants_par_niveau':
        // Nombre d'étudiants par niveau
        $query = "
            SELECT 
                Niveau, 
                COUNT(*) AS nombre_etudiants
            FROM 
                etudiants
            GROUP BY 
                Niveau;
        ";
        break;

    case 'mentions_par_niveau':
        // Taux de mention par niveau
        $query = "
            SELECT 
                e.Niveau, 
                b.mention, 
                COUNT(*) AS nombre_mentions
            FROM 
                bulletins b
            JOIN 
                etudiants e ON b.etudiant_id = e.id
            GROUP BY 
                e.Niveau, b.mention;
        ";
        break;

    case 'cours_par_notes':
        // Cours ayant le plus de notes
        $query = "
            SELECT 
                c.nom AS nom_cours, 
                COUNT(n.id) AS nombre_notes
            FROM 
                notes n
            JOIN 
                cours c ON n.cours_id = c.id
            GROUP BY 
                c.nom
            ORDER BY 
                nombre_notes DESC
            LIMIT 10;
        ";

        case 'versements_par_jour':
            $query = "
                SELECT
                    DATE(v.date_versement) AS date,
                    SUM(v.montant) AS total_versements
                FROM
                    versements v
                GROUP BY
                    DATE(v.date_versement);
            ";
            case 'tendance_versements':
                $query = "
                    SELECT
                        DATE(v.date_versement) AS date,
                        SUM(v.montant) AS total_versements
                    FROM
                        versements v
                    GROUP BY
                        DATE(v.date_versement)
                    ORDER BY
                        DATE(v.date_versement);
                ";
                break;
        break;

    default:
        echo json_encode(['error' => 'Type de statistique invalide.']);
        exit;
}

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
