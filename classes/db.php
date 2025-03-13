<?php

class Database {
    private $host = "localhost";
    private $db_name = "gitgud";
    private $username = "root";
    private $password = "";
    protected $conn;

    function connect() {
        try {
            $this->conn = new PDO("mysql:host=".$this->host.";dbname=".$this->db_name, $this->username, $this->password);
            // $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // $this->conn->setAttribute(PDO::ATTR_AUTOCOMMIT, false); // Disable autocommit to manage transactions manually
            return $this->conn;
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }
}