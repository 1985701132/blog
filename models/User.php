<?php
    namespace models;
    use PDO;
    class User{
        public $pdo;
        public function __construct()
        {
            $this->pdo = new PDO('mysql:host=127.0.0.1;dbname=blog','root','');
            $this->pdo->exec('SET NAMES utf8');
        }
        public function add($email,$password)
        {
            $stmt = $this->pdo->prepare("INSERT INTO users (email,password) VALUES(?,?)");
            return $stmt->execute([
                $email,
                $password,
            ]);
        }
    }
?>