<?php

class Database {
    
    private $host = '127.0.0.1';
    private $dbName = 'basesdedonnes';
    private $username = 'root';
    private $password = 'Callita4';
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


