<?php

namespace App\Core\Foundation;


use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Core\Views\IRenderable;


/**
 * Class Application
 * @package App\Core\Foundation
 */
class Application
{
    protected static $container;
    private $current_controller = null;

    public function __construct(ContainerBuilder $container)
    {
        self::$container = $container;

        // Importing Helper Functions
        require __DIR__ . '/../Support/Helpers/helpers.php';

        // Getting Router
        $router = $container->get('router');

        // Getting HttpKernel
        $httpKernel = $container->get('kernel');

        // Getting current route
        $route = $router->getRoute();

        // Handling request through HttpKernel
        $response = $httpKernel
            ->routeMiddleware($route->getMiddleware())
            ->handle(self::getInstance(Request::class), function ($request) use ($container, $route) {

                // Getting route controller
                $className = "App\\Http\\Controllers\\" . $route->getController();

                // Building route controller
                $this->current_controller = $container->get('factory')->make($className);

                // Getting route controller function
                $functionName = $route->getFunction();

                // Injecting dependencies
                $function = new \ReflectionMethod($className, $functionName);
                $routeParameters = $route->getParameterValues();
                $parameters = [];

                foreach ($function->getParameters() as $parameter) {
                    $type = $parameter->getType();

                    if (!is_null($type) && $container->has($type->getName())) {
                        $parameters[] = $container->get($type->getName());
                    } else {
                        if (empty($routeParameters)) continue;
                        $parameters[] = array_pop($routeParameters);
                    }
                }

                // Calling route function with injected parameters
                $response = call_user_func_array([$this->current_controller, $functionName], $parameters);

                return $response;
            });

        if (isset($response)) {
            if ($response instanceof IRenderable) {
                $response->render();
            } else {
                $res = Response::create($response);
                $res->send();
            }
        } else {
            throw new \Error('No response');
        }
    }

    public static function getInstance($abstract = null)
    {
        if (is_null($abstract)) {
            return self::$container;
        }

        return self::$container->get($abstract);
    }

}
