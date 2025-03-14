<?php
require_once __DIR__ . '/../models/Product.php';

require_once '/models/Product.php';

class ProductController {

    public function index() {
        $products = Product::getAllProducts();
        require_once 'app/views/products/index.view.php';
    }

    public function show($id) {
        $product = Product::getProductById($id);
        require_once 'app/views/products/product.view.php';
    }
}
?>
