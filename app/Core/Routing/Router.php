<?php

namespace App\Core\Routing;


/**
 * Class Router
 * @package App\Core\Routing
 */
class Router
{
    private static $_ROUTES = [];

    private $_url = null;
    private $_current_route;

    /**
     * Router constructor.
     */
    public function __construct()
    {
        // Importing routes
        require_once __DIR__ . '/../../../routes/web.php';

        // Processing URL
        $this->_url = new URL();

        // Finding route by URL
        $route = $this->findRouteByUrl($this->_url);

        $this->_current_route = !is_null($route) ? $route : new Route($_SERVER["REQUEST_METHOD"], $this->_url->getUrl(), 'ErrorController', 'index');
    }

    /**
     * @return array
     */
    public function getRoutes(): array
    {
        return self::$_ROUTES;
    }

    /**
     * @return Route
     */
    public function getRoute(): Route
    {
        return $this->_current_route;
    }

    /**
     * @param URL $url
     * @return Route|null
     */
    public function findRouteByUrl(URL $url): ?Route
    {

        // Searching for matching route by URL
        foreach (self::$_ROUTES as $route) {

            // Skip if request method doesn't match
            if ($url->getMethod() !== $route->getMethod()) continue;

            // Testing if url matches
            if (preg_match("#^" . $route->getUrlRegex() . "$#", $url->getUrl())) {

                // Setting route variables
                $route->setParametersByIndex(explode('/', $url->getUrl()));

                // Returning matched route for chaining
                return $route;
            }
        }

        // No matching route
        return null;
    }

    /**
     * @param string $name
     * @return Route|null
     */
    public static function findRouteByName(string $name): ?Route
    {
        $find = null;

        array_filter(self::$_ROUTES, function ($route) use ($name, &$find) {
            if ($route->getName() === $name) {
                $find = $route;
            }
        });
        return $find;
    }

    /**
     * @param string $url
     * @param $controller
     * @param string $method
     * @return Route
     */
    private static function addRoute(string $url, $controller, string $method): Route
    {
        // Get controller as index 0 and function as index 1
        $controller = explode("@", $controller);

        $params = [];

        // Trim unnecessary slashes
        if (strlen($url) > 1) {
            $url = ltrim($url, "/");
            $url = rtrim($url, "/");
        }

        // Explode for easy management
        $url = explode('/', $url);

        // Check for parameters
        foreach ($url as $key => $val) {
            if (preg_match("/^{[A-z0-9]+}$/", $val)) {
                $name = str_replace(['{', '}'], '', $val);
                $params[$name] = [
                    'index' => $key,
                    'value' => null
                ];
            }
        }

        // Glue back together
        $url = implode('/', $url);

        $route = new Route($method, $url, $controller[0], $controller[1], $params);

        self::$_ROUTES[] = $route;

        return $route;
    }

    /**
     * @param string $url
     * @param $controller
     * @return Route
     */
    public static function get(string $url, $controller): Route
    {
        return self::addRoute($url, $controller, 'GET');
    }

    /**
     * @param string $url
     * @param $controller
     * @return Route
     */
    public static function post(string $url, $controller): Route
    {
        return self::addRoute($url, $controller, 'POST');
    }

    /**
     * @param string $url
     * @param $controller
     * @return Route
     */
    public static function patch(string $url, $controller): Route
    {
        return self::addRoute($url, $controller, 'PATCH');
    }

    /**
     * @param string $url
     * @param $controller
     * @return Route
     */
    public static function put(string $url, $controller): Route
    {
        return self::addRoute($url, $controller, 'PUT');
    }

    /**
     * @param string $url
     * @param $controller
     * @return Route
     */
    public static function delete(string $url, $controller): Route
    {
        return self::addRoute($url, $controller, 'DELETE');
    }
}
