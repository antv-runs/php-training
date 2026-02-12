<?php

require_once __DIR__ . '/../../config/Database.php';

class ProductModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::connect();
    }

    public function getAll()
    {
        $stmt = $this->conn->query("SELECT * FROM products");
        // PDO::FETCH_ASSOC ensures each row is returned as an associative array
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}