<?php

require_once 'config/database.php';

class User {
    public $id;
    public $firstname;
    public $lastname;
    public $email;
    public $password;

    public static function createUser($firstname, $lastname, $email, $password) {
        global $pdo;
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (firstname, lastname, email, password) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$firstname, $lastname, $email, $hashedPassword]);
    }

    public static function authenticateUser($email, $password) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
}
?>
