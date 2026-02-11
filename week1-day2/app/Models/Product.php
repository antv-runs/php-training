<?php
require_once "../app/Core/Database.php";

class ProductModel {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function getProduct($id) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getColors($id) {
        $stmt = $this->db->prepare(
            "SELECT product FROM product_colors WHERE product_id=?"
        );
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getSizes($id) {
        $stmt = $this->db->prepare(
            "SELECT size FROM product_sizes WHERE product_id=?"
        );
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}