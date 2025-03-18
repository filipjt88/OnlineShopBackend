<?php
session_start();
require_once '../app/models/Order.php';

if (!isset($_SESSION['user_id'])) {
    die("Morate biti prijavljeni.");
}

$orders = Order::getOrdersByUser($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Moje Porudžbine</title>
</head>
<body>
    <h2>Moje Porudžbine</h2>

    <?php if (empty($orders)) : ?>
        <p>Nema porudžbina.</p>
    <?php else : ?>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Ukupna Cena</th>
                <th>Status</th>
                <th>Datum</th>
            </tr>
            <?php foreach ($orders as $order) : ?>
                <tr>
                    <td><?= $order['id'] ?></td>
                    <td><?= number_format($order['total_price'], 2) ?> USD</td>
                    <td><?= ucfirst($order['status']) ?></td>
                    <td><?= $order['created_at'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
