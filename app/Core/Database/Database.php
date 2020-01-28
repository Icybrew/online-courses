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

    /**
     * Database connection
     * @var PDO
     */
    protected $connection;

    /**
     * Database constructor.
     */
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

        $this->connection = new PDO($dsn, $username, $password, $options);
    }

    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param string $table
     * @return QueryBuilder
     */
    public function table(string $table): QueryBuilder
    {
        return (new QueryBuilder())->from($table);
    }

    /**
     * @param string $query
     * @return \PDOStatement|null
     */
    public function queryRaw(string $query): ?\PDOStatement
    {
        $statement = $this->connection->query($query);
        return $statement;
    }

    /**
     * @param string $query
     * @return \App\Core\Support\Collection|mixed
     */
    public function queryObject(string $query)
    {
        $statement = $this->connection->query($query);

        if ($statement->rowCount() <= 1) {
            return $statement->fetchObject();
        } else {
            return collect($statement->fetchAll(PDO::FETCH_OBJ));
        }
    }

    /**
     * @param string $query
     * @return \App\Core\Support\Collection|mixed
     */
    public function queryArray(string $query)
    {
        $statement = $this->connection->query($query);

        if ($statement->rowCount() <= 1) {
            return $statement->fetch();
        } else {
            return collect($statement->fetchAll());
        }
    }
}
