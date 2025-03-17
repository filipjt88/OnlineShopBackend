<?php
require_once __DIR__ . '/../models/Cart.php';

class CartController {
    public function add() {
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['product_id'], $data['quantity'])) {
            $cart = Cart::addToCart($data['product_id'], $data['quantity']);
            echo json_encode(["message" => "Proizvod dodat u korpu!", "cart" => $cart]);
        } else {
            echo json_encode(["error" => "ID proizvoda i količina su obavezni!"]);
        }
    }

    public function get() {
        echo json_encode(["cart" => Cart::getCart()]);
    }

    public function remove() {
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['product_id'])) {
            $cart = Cart::removeFromCart($data['product_id']);
            echo json_encode(["message" => "Proizvod uklonjen iz korpe!", "cart" => $cart]);
        } else {
            echo json_encode(["error" => "ID proizvoda je obavezan!"]);
        }
    }

    public function clear() {
        $cart = Cart::clearCart();
        echo json_encode(["message" => "Korpa je ispražnjena!", "cart" => $cart]);
    }
}
?>
