<?php
namespace App;
use PDO;
use PDOException;
use FPDF;
use Exception;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Notes
{
    // Propriété pour stocker la connexion à la base de données
    private $db;

    /**
     * Constructeur de la classe Notes.
     * Initialise la connexion à la base de données.
     *
     * @param PDO $db Instance de PDO pour la connexion à la base de données.
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Enregistre une nouvelle note dans la base de données.
     *
     * @param array $data Tableau associatif contenant les données de la note.
     * @return bool Retourne true si l'insertion a réussi, sinon false.
     */
    public function enregistrerNotes($data)
    {
        try {
            $query = "INSERT INTO notes (etudiant_id, cours_id, type_note, valeur, annee_scolaire) 
                      VALUES (:etudiant_id, :cours_id, :type_note, :valeur, :annee_scolaire)";
            $stmt = $this->db->prepare($query);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de l'enregistrement de la note : " . $e->getMessage());
        }
    }

    /**
     * Ajoute une note pour un étudiant.
     *
     * @param int $etudiant_id ID de l'étudiant.
     * @param int $cours_id ID du cours.
     * @param string $type_note Type de note (CC, TP, Exam, Rattrapage).
     * @param float $valeur Valeur de la note.
     * @param string $annee_scolaire Année scolaire.
     * @return bool Retourne true si l'ajout a réussi, sinon false.
     */
    public function ajouterNote($etudiant_id, $cours_id, $type_note, $valeur, $annee_scolaire)
    {
        try {
            $query = "INSERT INTO notes (etudiant_id, cours_id, type_note, valeur, annee_scolaire, created_at, updated_at) 
                      VALUES (:etudiant_id, :cours_id, :type_note, :valeur, :annee_scolaire, NOW(), NOW())";
            $stmt = $this->db->prepare($query);

            $stmt->bindParam(':etudiant_id', $etudiant_id, PDO::PARAM_INT);
            $stmt->bindParam(':cours_id', $cours_id, PDO::PARAM_INT);
            $stmt->bindParam(':type_note', $type_note, PDO::PARAM_STR);
            $stmt->bindParam(':valeur', $valeur, PDO::PARAM_STR);
            $stmt->bindParam(':annee_scolaire', $annee_scolaire, PDO::PARAM_STR);

            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Erreur lors de l'ajout de la note.");
            }
        } catch (PDOException $e) {
            throw new Exception("Erreur SQL : " . $e->getMessage());
        }
    }

    /**
     * Liste toutes les notes avec les détails des étudiants et des cours.
     *
     * @return array Tableau associatif contenant les notes et les détails associés.
     */
    public function listerNotes()
    {
        try {
            $query = "SELECT 
                        n.id, 
                        e.nom AS etudiant, 
                        e.niveau, 
                        c.nom AS cours, 
                        n.type_note, 
                        n.valeur, 
                        n.annee_scolaire, 
                        n.created_at
                      FROM notes n
                      LEFT JOIN etudiants e ON n.etudiant_id = e.id
                      LEFT JOIN cours c ON n.cours_id = c.id";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des notes : " . $e->getMessage());
        }
    }

    /**
     * Supprime une note par son ID.
     *
     * @param int $id ID de la note à supprimer.
     * @return bool Retourne true si la suppression a réussi, sinon false.
     */
    public function supprimerNote($id)
    {
        try {
            if (!is_numeric($id)) {
                throw new Exception("L'ID fourni n'est pas valide.");
            }

            $query = "DELETE FROM notes WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Erreur lors de la suppression de la note.");
            }
        } catch (PDOException $e) {
            throw new Exception("Erreur SQL : " . $e->getMessage());
        }
    }

    /**
     * Récupère la liste des cours par niveau.
     *
     * @param string $niveau Niveau des cours à récupérer.
     * @return array Tableau associatif contenant les cours.
     */
    public function getCoursParNiveau($niveau)
    {
        try {
            $query = "SELECT id, nom FROM cours WHERE niveau = :niveau";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['niveau' => $niveau]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des cours : " . $e->getMessage());
        }
    }

    /**
     * Calcule la moyenne d'un étudiant pour un cours donné.
     *
     * @param int $cours_id ID du cours.
     * @param int $etudiant_id ID de l'étudiant.
     * @return float Moyenne calculée.
     */
    public function calculerMoyenneParCours($cours_id, $etudiant_id)
    {
        try {
            $query = "SELECT type_note, valeur FROM notes WHERE cours_id = :cours_id AND etudiant_id = :etudiant_id";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['cours_id' => $cours_id, 'etudiant_id' => $etudiant_id]);
            $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $moyenne = 0;
            $poids = ['CC' => 0.2, 'TP' => 0.4, 'Exam' => 0.4];
            $rattrapage = null;

            foreach ($notes as $note) {
                if ($note['type_note'] === 'Rattrapage') {
                    $rattrapage = $note['valeur'];
                } else {
                    $moyenne += $note['valeur'] * $poids[$note['type_note']];
                }
            }

            if ($rattrapage !== null) {
                $moyenne = ($moyenne - ($notes['Exam'] ?? 0) * 0.4) + ($rattrapage * 0.4);
            }

            return $moyenne;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors du calcul de la moyenne : " . $e->getMessage());
        }
    }
}