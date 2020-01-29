<?php


namespace App\Core\Facades;


/**
 * Class Route
 * @package App\Core\Facades
 *
 * @method static \App\Core\Routing\Route get(string $url, $controller)
 * @method static \App\Core\Routing\Route post(string $url, $controller)
 * @method static \App\Core\Routing\Route put(string $url, $controller)
 * @method static \App\Core\Routing\Route patch(string $url, $controller)
 * @method static \App\Core\Routing\Route delete(string $url, $controller)
 */
class Route extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'router';
    }
}
