<?php

use App\Core\Config\Config;
use App\Core\Foundation\Application;

use App\Core\Factories\ViewFactory;
use App\Core\Routing\Redirect;
use App\Core\Routing\Router;

/**
 *
 * Global Helper functions
 *
 */

if (!function_exists('dd')) {

    function dd($var)
    {
        echo "<pre style='background-color: black; color: white; padding: 25px; overflow: visible;'>";
        var_dump($var);
        echo "</pre>";
        die();
    }
}

if (!function_exists('dc')) {

    function dc($var)
    {
        echo "<pre style='background-color: black; color: white; padding: 25px; overflow: visible;'>";
        var_dump($var);
        echo "</pre>";
        echo '</pre>';
    }
}

if (!function_exists('redirect')) {

    function redirect(string $to = null)
    {
        return new Redirect($to);
    }
}

if (!function_exists('view')) {

    function view(string $name, array $data = [])
    {
        $factory = new ViewFactory();

        return $factory->make($name, $data);
    }
}

if (!function_exists('route')) {

    function route(string $name, array $param = [])
    {
        $request = app(\Symfony\Component\HttpFoundation\Request::class);

        $route = Router::findRouteByName($name);
        if (is_null($route)) return null;

        $scheme = $request->server->get('REQUEST_SCHEME') . '://';
        $host = $request->server->get('SERVER_NAME');
        $url = $route->getUrl($param);

        return sprintf('%s%s%s%s', $scheme, $host, Config::get('app', 'root'), $url);
    }
}

if (!function_exists('collect')) {

    function collect($items)
    {
        return new \App\Core\Support\Collection($items);
    }
}

if (!function_exists('app')) {

    function app($abstract = null)
    {
        if (is_null($abstract)) {
            return Application::getInstance();
        }

        return Application::getInstance($abstract);
    }
}
