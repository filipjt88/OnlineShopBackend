<?php

require_once 'app/models/User.php';

class UserController {

    public function register() {
        require_once 'app/views/auth/register.view.php';
    }

    public function login() {
        require_once 'app/views/auth/login.view.php';
    }

    public function store() {
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        User::createUser($firstname, $lastname, $email, $password);
        header("Location: /login");
    }

    public function authenticate() {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $user = User::authenticateUser($email, $password);
        
        if ($user) {
            $_SESSION['user'] = $user;
            header("Location: /products");
        } else {
            echo "Invalid login credentials!";
        }
    }
}
?>
