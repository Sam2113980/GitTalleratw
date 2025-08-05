<?php declare(strict_types=1);
namespace App\Config;
use PDO;

class Database //Cadena de conexion a la base de datos
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) 
            {
            $host = 'localhost';
            $db = 'project_db';
            $user = 'admin';
            $password = 'admin';
            $charset = 'utf8mb4';
            
              $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
              $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
             ];
            self::$instance = new PDO($dsn, $user, $password, $options);
            }
      return self::$instance;

    }
}