<?php
namespace models;

use PDO;

class Redbag
{
    public $pdo;
    public function __construct()
    {
        $this->pdo = new PDO('mysql:host=127.0.0.1;dbname=blog','root','');
        $this->pdo->exec('SET NAMES utf8');
    }
    public function create($userId)
    {
        $stmt = $this->pdo->prepare("INSERT INTO redbags(user_id) VALUES(?)");
        $stmt->execute([$userId]);
    } 
}