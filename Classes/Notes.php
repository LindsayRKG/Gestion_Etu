<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Notes
{
    private $db;


    public function __construct($db)
    {
        $this->db = $db;
    }

    public function enregistrerNotes($data)
    {
        $query = "INSERT INTO notes (etudiant_id, cours_id, type_note, valeur, annee_scolaire) 
                  VALUES (:etudiant_id, :cours_id, :type_note, :valeur, :annee_scolaire)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute($data);
    }

    // Ajouter une note pour un étudiant

    public function ajouterNote($etudiant_id, $cours_id, $type_note, $valeur, $annee_scolaire)
    {
        try {
            $query = "INSERT INTO notes (etudiant_id, cours_id, type_note, valeur, annee_scolaire, created_at, updated_at) 
                      VALUES (:etudiant_id, :cours_id, :type_note, :valeur, :annee_scolaire, NOW(), NOW())";
            $stmt = $this->db->prepare($query);

            $stmt->bindParam(':etudiant_id', $etudiant_id);
            $stmt->bindParam(':cours_id', $cours_id);
            $stmt->bindParam(':type_note', $type_note);
            $stmt->bindParam(':valeur', $valeur);
            $stmt->bindParam(':annee_scolaire', $annee_scolaire);

            if ($stmt->execute()) {
                echo "Note ajoutée avec succès.";
                return true;
            } else {
                $errorInfo = $stmt->errorInfo();
                echo "Erreur SQL : " . $errorInfo[2];
                return false;
            }
        } catch (Exception $e) {
            echo 'Erreur : ' . $e->getMessage();
            return false;
        }
    }

    public function listerNotes()
    {
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
    }

   
    // Méthode pour supprimer une note
    public function supprimerNote($id) {
        // Vérifier que l'ID est un entier valide
        if (!is_numeric($id)) {
            echo "L'ID fourni n'est pas valide.";
            return false;
        }

        // Requête SQL pour supprimer la note
        $query = "DELETE FROM notes WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Exécuter la requête et vérifier si la suppression a réussi
        if ($stmt->execute()) {
            return true; // La suppression a réussi
        } else {
            echo "Erreur lors de la suppression de la note.";
            return false; // La suppression a échoué
        }
    }


    public function getCoursParNiveau($niveau)
    {
        $query = "SELECT id, nom FROM cours WHERE niveau = :niveau";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['niveau' => $niveau]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }




    public function calculerMoyenneParCours($cours_id, $etudiant_id)
    {
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
    }
}
