use etudiants;versementsetudiants
CREATE TABLE IF NOT EXISTS versements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    matricule_etudiant VARCHAR(50) NOT NULL,
    montant DECIMAL(10, 2) NOT NULL,
    matricule_versement VARCHAR(100) NOT NULL,
    date_versement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (matricule_etudiant) REFERENCES etudiants(matricule)
);