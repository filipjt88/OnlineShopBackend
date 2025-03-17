<?php
session_start();
require_once '../app/models/Cart.php';
require_once '../app/models/Product.php';

// Učitaj proizvode iz korpe
$cart = Cart::getCart();
$totalPrice = 0;
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <script src="https://www.paypal.com/sdk/js?client-id=TVOJ_PAYPAL_CLIENT_ID&currency=USD"></script>
</head>
<body>
    <h2>Vaša korpa</h2>

    <?php if (empty($cart)) : ?>
        <p>Vaša korpa je prazna.</p>
    <?php else : ?>
        <table border="1">
            <tr>
                <th>Proizvod</th>
                <th>Količina</th>
                <th>Cena</th>
                <th>Ukupno</th>
            </tr>
            <?php foreach ($cart as $productId => $quantity) : 
                $product = Product::getById($productId);
                $subtotal = $product['price'] * $quantity;
                $totalPrice += $subtotal;
            ?>
                <tr>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= $quantity ?></td>
                    <td><?= number_format($product['price'], 2) ?> USD</td>
                    <td><?= number_format($subtotal, 2) ?> USD</td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h3>Ukupna cena: <?= number_format($totalPrice, 2) ?> USD</h3>

        <div id="paypal-button-container"></div>

        <script>
        paypal.Buttons({
            createOrder: function(data, actions) {
                return fetch('http://localhost/OnlineShopBackend/public/paypal/create', {
                    method: 'POST',
                })
                .then(response => response.json())
                .then(order => order.id);
            },
            onApprove: function(data, actions) {
                return fetch(`http://localhost/OnlineShopBackend/public/paypal/capture?orderId=${data.orderID}`, {
                    method: 'POST',
                })
                .then(response => response.json())
                .then(response => alert(response.message));
            }
        }).render('#paypal-button-container');
        </script>

    <?php endif; ?>
</body>
</html>
