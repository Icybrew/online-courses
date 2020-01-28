<?php


namespace App\Core\Facades;


/**
 * Class DB
 * @package App\Core\Facades
 *
 * @method static queryRaw(string $query)
 * @method static \App\Core\Support\Collection queryObject(string $query)
 * @method static \App\Core\Support\Collection queryArray(string $query)
 */
class DB extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'database';
    }
}