<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/Cart.php';

class Order {
    public static function createOrder($userId) {
        global $pdo;

        $cart = Cart::getCart();
        if (empty($cart)) {
            return ["error" => "Korpa je prazna!"];
        }

        // Izračunaj ukupnu cenu
        $totalPrice = 0;
        foreach ($cart as $productId => $quantity) {
            $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($product) {
                $totalPrice += $product['price'] * $quantity;
            }
        }

        // Kreiraj narudžbinu
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price) VALUES (?, ?)");
        $stmt->execute([$userId, $totalPrice]);
        $orderId = $pdo->lastInsertId();

        // Sačuvaj stavke narudžbine
        foreach ($cart as $productId => $quantity) {
            $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($product) {
                $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                $stmt->execute([$orderId, $productId, $quantity, $product['price']]);
            }
        }

        // Isprazni korpu nakon kupovine
        Cart::clearCart();

        return ["message" => "Narudžbina uspešno kreirana!", "order_id" => $orderId];
    }

    public static function getUserOrders($userId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
