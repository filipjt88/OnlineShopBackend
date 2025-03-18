<?php
require_once '../app/models/Order.php';
require_once '../app/models/Cart.php';
require_once '../app/models/User.php';

class PaypalController {
    public static function create() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Niste prijavljeni.']);
            return;
        }

        $cart = Cart::getCart();
        $totalPrice = 0;

        foreach ($cart as $productId => $quantity) {
            $product = Product::getById($productId);
            $totalPrice += $product['price'] * $quantity;
        }

        if ($totalPrice == 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Korpa je prazna.']);
            return;
        }

        $orderId = Order::createOrder($_SESSION['user_id'], $cart, $totalPrice);

        $_SESSION['order_id'] = $orderId;

        echo json_encode(['id' => uniqid("PAYPAL_ORDER_")]); // Simulacija PayPal order ID-a
    }

    public static function capture() {
        session_start();
        if (!isset($_SESSION['order_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Porudžbina nije pronađena.']);
            return;
        }

        $db = Database::connect();
        $stmt = $db->prepare("UPDATE orders SET status = 'completed' WHERE id = ?");
        $stmt->execute([$_SESSION['order_id']]);

        self::sendConfirmationEmail($_SESSION['user_id'], $_SESSION['order_id']);

        unset($_SESSION['cart']);
        unset($_SESSION['order_id']);

        echo json_encode(['message' => 'Uspešno plaćeno!']);
    }

    private static function sendConfirmationEmail($userId, $orderId) {
        $user = User::getById($userId);
        $to = $user['email'];
        $subject = "Potvrda kupovine";
        $message = "Vaša porudžbina #$orderId je uspešno završena.";
        mail($to, $subject, $message);
    }
}
