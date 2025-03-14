<?php

session_start();

require_once '../app/controllers/ProductController.php';
require_once '../app/controllers/UserController.php';

$uri = $_SERVER['REQUEST_URI'];
$uriParts = explode('/', trim($uri, '/'));

if ($uriParts[0] === 'login' && isset($uriParts[1]) && $uriParts[1] === 'authenticate') {
    $controller = new UserController();
    $controller->authenticate();
} elseif ($uriParts[0] === 'register') {
    $controller = new UserController();
    $controller->store();
} elseif ($uriParts[0] === 'products') {
    $controller = new ProductController();
    $controller->index();
} elseif (isset($uriParts[0]) && is_numeric($uriParts[0])) {
    $controller = new ProductController();
    $controller->show($uriParts[0]);
}
