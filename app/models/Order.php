<?php
require_once '../app/core/Database.php';

class Order {
    public static function createOrder($userId, $cart, $totalPrice) {
        $db = Database::connect();
        $stmt = $db->prepare("INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, 'pending')");
        $stmt->execute([$userId, $totalPrice]);
        $orderId = $db->lastInsertId();

        foreach ($cart as $productId => $quantity) {
            $productStmt = $db->prepare("SELECT price FROM products WHERE id = ?");
            $productStmt->execute([$productId]);
            $product = $productStmt->fetch();

            $stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$orderId, $productId, $quantity, $product['price']]);
        }

        return $orderId;
    }

    public static function getOrdersByUser($userId) {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM orders WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
