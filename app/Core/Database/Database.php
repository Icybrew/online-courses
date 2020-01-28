<?php

namespace App\Core\Database;

use App\Core\Config\Config;
use PDO;


/**
 * Class Database
 * @package App\Core\Database
 */
class Database
{
    protected $pdo;

    protected $error;

    public function __construct()
    {
        $hostname = Config::get('database', 'hostname');
        $username = Config::get('database', 'username');
        $password = Config::get('database', 'password');
        $database = Config::get('database', 'database');
        $charset = Config::get('database', 'charset');

        $options = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];

        $dsn = "mysql:host=$hostname;dbname=$database;charset=$charset";

        $this->pdo = new PDO($dsn, $username, $password, $options);
    }

    public function queryRaw($query)
    {
        $statement = $this->pdo->query($query);
        return $statement;
    }

    public function queryObject($query)
    {
        $statement = $this->pdo->query($query);

        if ($statement->rowCount() <= 1) {
            return $statement->fetchObject();
        } else {
            return collect($statement->fetchAll(PDO::FETCH_OBJ));
        }
    }

    public function queryArray($query)
    {
        $statement = $this->pdo->query($query);

        if ($statement->rowCount() <= 1) {
            return $statement->fetch();
        } else {
            return $statement->fetchAll();
        }
    }
}
