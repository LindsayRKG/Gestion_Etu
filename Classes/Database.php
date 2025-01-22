<?php
namespace App;
use PDO;
use PDOException;
use FPDF;

class Database {
    
    private $host = 'localhost';
    private $dbName = 'basededonnes';
    private $username = 'root';
    private $password = '';
    private $connection;

    // Méthode pour obtenir la connexion
    public function getConnection() {
        $this->connection = null;

        try {
            $this->connection = new PDO(
                "mysql:host={$this->host};dbname={$this->dbName};charset=utf8",
                $this->username,
                $this->password
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Erreur de connexion : " . $exception->getMessage();
        }

        return $this->connection;
    }
}


