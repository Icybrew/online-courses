<?php


namespace App\Core\Facades;


use App\Core\Database\QueryBuilder;
use App\Core\Support\Collection;

/**
 * Class DB
 * @package App\Core\Facades
 *
 * @method static \PDO getConnection()
 * @method static QueryBuilder table(string $table)
 * @method static \PDOStatement queryRaw(string $query)
 * @method static Collection queryObject(string $query)
 * @method static Collection queryArray(string $query)
 */
class DB extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'database';
    }
}