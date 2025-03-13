<?php foreach ($products as $product): ?>
    <div class="product">
        <h3><?php echo $product['name']; ?></h3>
        <p><?php echo $product['description']; ?></p>
        <p><?php echo $product['price']; ?> $</p>
        <a href="/product/<?php echo $product['id']; ?>">View</a>
    </div>
<?php endforeach; ?>
