<?php


namespace App\Core\Facades;


abstract class Facade
{
    public static function __callStatic($method, $arguments)
    {
        $instance = app(static::getFacadeAccessor());

        if (!$instance) {
            throw new \RuntimeException('Facade accessor not found');
        }

        return $instance->$method(...$arguments);
    }

    protected static function getFacadeAccessor(): string
    {
        throw new \RuntimeException('Facade does not implement getFacadeAccessor method.');
    }
}
