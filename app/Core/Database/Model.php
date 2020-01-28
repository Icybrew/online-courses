<?php

namespace App\Core\Database;


/**
 * Class Model
 * @package App\Core\Database
 */
abstract class Model
{
    /**
     * Database table name
     * @var
     */
    protected $table;

    /**
     * Database table primary key
     * @var string
     */
    protected $primary_key = 'id';

    public function __construct()
    {
        // Base Model
    }

    /**
     * Database table name
     * @return string
     * @throws \ReflectionException
     */
    public function getTable()
    {
        return ($this->table != null) ? $this->table : (new \ReflectionClass(get_called_class()))->getShortName();
    }

    /**
     * @return string
     */
    private function getPrimaryKey()
    {
        return $this->primary_key;
    }

    /**
     * @param $method
     * @param $arguments
     * @return mixed
     * @throws \ReflectionException
     */
    public function __call($method, $arguments)
    {
        return (new QueryBuilder())->from($this->getTable())->$method(...$arguments);
    }

    /**
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($method, $arguments)
    {
        return (new static)->$method(...$arguments);
    }
}
