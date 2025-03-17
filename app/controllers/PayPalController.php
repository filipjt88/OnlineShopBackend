<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/Order.php';

class PayPalController {
    private $config;

    public function __construct() {
        $this->config = require __DIR__ . '/../config/paypal.php';
    }

    // Generiše PayPal token
    private function getAccessToken() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->config['base_url'] . "/v1/oauth2/token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $this->config['client_id'] . ":" . $this->config['secret']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);

        return $response['access_token'] ?? null;
    }

    // Kreira PayPal narudžbinu
    public function createOrder() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(["error" => "Morate biti prijavljeni!"]);
            return;
        }

        $token = $this->getAccessToken();
        if (!$token) {
            echo json_encode(["error" => "Greška u autentifikaciji PayPal-a."]);
            return;
        }

        $cart = Cart::getCart();
        if (empty($cart)) {
            echo json_encode(["error" => "Korpa je prazna."]);
            return;
        }

        // Generišemo JSON za PayPal API
        $items = [];
        $totalPrice = 0;
        foreach ($cart as $productId => $quantity) {
            global $pdo;
            $stmt = $pdo->prepare("SELECT name, price FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($product) {
                $items[] = [
                    "name" => $product['name'],
                    "unit_amount" => ["currency_code" => "USD", "value" => $product['price']],
                    "quantity" => $quantity
                ];
                $totalPrice += $product['price'] * $quantity;
            }
        }

        $orderData = [
            "intent" => "CAPTURE",
            "purchase_units" => [[
                "amount" => ["currency_code" => "USD", "value" => $totalPrice],
                "items" => $items
            ]]
        ];

        // Slanje zahteva PayPal API-ju
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->config['base_url'] . "/v2/checkout/orders");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $token",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($orderData));

        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);

        echo json_encode($response);
    }

    // Obrada PayPal plaćanja
    public function captureOrder($orderId) {
        $token = $this->getAccessToken();
        if (!$token) {
            echo json_encode(["error" => "Greška u autentifikaciji PayPal-a."]);
            return;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->config['base_url'] . "/v2/checkout/orders/$orderId/capture");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $token",
            "Content-Type: application/json"
        ]);

        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if (isset($response['status']) && $response['status'] === "COMPLETED") {
            session_start();
            Order::createOrder($_SESSION['user_id']);
            echo json_encode(["message" => "Plaćanje uspešno!"]);
        } else {
            echo json_encode(["error" => "Plaćanje nije uspelo."]);
        }
    }
}
?>
