<?php
session_start();

class Cart {
    public static function addToCart($productId, $quantity) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }

        return $_SESSION['cart'];
    }

    public static function getCart() {
        return isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    }

    public static function removeFromCart($productId) {
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
        }
        return $_SESSION['cart'];
    }

    public static function clearCart() {
        $_SESSION['cart'] = [];
        return $_SESSION['cart'];
    }
}
?>
