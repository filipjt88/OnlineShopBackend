<?php

require_once 'config/database.php';

class Product {
    public $id;
    public $name;
    public $price;
    public $description;

    public static function getAllProducts() {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM products");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getProductById($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
