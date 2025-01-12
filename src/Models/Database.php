<?php

namespace App\Models;
use PDO;
use PDOException;

class Database
{
    
    public static function getConnection()
    {

        $dsn = "mysql:host=localhost;dbname=ifconecta;charset=utf8";
        $pdo = new PDO($dsn, 'root', '@Kaiki0403');
        return $pdo;

    }
}