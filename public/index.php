<?php
require_once '../app/controllers/ProductController.php';
require_once '../app/controllers/UserController.php';
require_once '../app/controllers/CartController.php';
require_once '../app/controllers/OrderController.php';

$requestUri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

if ($requestUri[0] === 'products') {
    $controller = new ProductController();
    if (isset($requestUri[1]) && is_numeric($requestUri[1])) {
        $controller->show($requestUri[1]);
    } else {
        $controller->index();
    }
} elseif ($requestUri[0] === 'users') {
    $controller = new UserController();
    if ($requestUri[1] === 'register') {
        $controller->register();
    } elseif ($requestUri[1] === 'login') {
        $controller->login();
    }
} elseif ($requestUri[0] === 'cart') {
    $controller = new CartController();
    if ($requestUri[1] === 'add') {
        $controller->add();
    } elseif ($requestUri[1] === 'get') {
        $controller->get();
    } elseif ($requestUri[1] === 'remove') {
        $controller->remove();
    } elseif ($requestUri[1] === 'clear') {
        $controller->clear();
    }
} elseif ($requestUri[0] === 'orders') {
    $controller = new OrderController();
    if ($requestUri[1] === 'create') {
        $controller->create();
    } elseif ($requestUri[1] === 'user') {
        $controller->getUserOrders();
    }
}

elseif ($requestUri[0] === 'paypal') {
    $controller = new PayPalController();
    if ($requestUri[1] === 'create') {
        $controller->createOrder();
    } elseif ($requestUri[1] === 'capture' && isset($_GET['orderId'])) {
        $controller->captureOrder($_GET['orderId']);
    }
}

elseif ($requestUri[0] === 'checkout') {
    require_once '../views/checkout.view.php';
}


?>
