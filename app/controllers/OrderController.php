<?php
require_once __DIR__ . '/../models/Order.php';

class OrderController {
    public function create() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(["error" => "Morate biti prijavljeni da biste napravili narudžbinu!"]);
            return;
        }

        $order = Order::createOrder($_SESSION['user_id']);
        echo json_encode($order);
    }

    public function getUserOrders() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(["error" => "Morate biti prijavljeni da biste videli svoje narudžbine!"]);
            return;
        }

        $orders = Order::getUserOrders($_SESSION['user_id']);
        echo json_encode(["orders" => $orders]);
    }
}
?>
