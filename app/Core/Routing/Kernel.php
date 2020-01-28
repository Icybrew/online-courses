<?php


namespace App\Core\Routing;


use Closure;


/**
 * Class Kernel
 * @package App\Core\Routing
 */
abstract class Kernel implements IKernel
{
    protected $middleware;
    protected $routeMiddleware;

    public function __construct($middleware = null)
    {
        $this->middleware = $middleware ?? $this->middleware;
    }

    public function middleware($middleware)
    {
        if ($middleware instanceof Kernel) {
            $middleware = $middleware->toArray();
        }

        if ($middleware instanceof IKernel) {
            $middleware = [$middleware];
        }

        if (!is_array($middleware)) {
            throw new InvalidArgumentException(get_class($middleware) . " is not a valid Kernel middleware.");
        }

        return new static(array_merge($this->middleware, $middleware));
    }

    /**
     * @param $alias
     * @return $this
     * @throws \Exception
     */
    public function routeMiddleware($alias): self
    {
        if (is_iterable($alias)) {
            foreach ($alias as $middleware) {
                if (isset($this->routeMiddleware[$middleware])) {
                    $this->middleware[] = $this->routeMiddleware[$middleware];
                } else {
                    throw new \Exception("Route middleware '$middleware' not found!");
                }
            }
        } else {
            if (isset($this->routeMiddleware[$alias])) {
                $this->middleware[] = $this->routeMiddleware[$alias];
            } else {
                throw new \Exception("Route middleware '$alias' not found!");
            }
        }

        return $this;
    }

    /**
     * Handle Request
     * @param $object
     * @param Closure $core
     * @return mixed
     */
    public function handle($object, Closure $core)
    {
        $coreFunction = $this->createCoreFunction($core);

        $middleware = array_reverse($this->middleware);

        $completeKernel = array_reduce($middleware, function ($nextLayer, $layer) {
            return $this->createMiddleware($nextLayer, $layer);
        }, $coreFunction);

        return $completeKernel($object);
    }

    /**
     * Get the middleware's of this HttpKernel, can be used to merge with another HttpKernel
     * @return array
     */
    public function toArray(): array
    {
        return $this->middleware;
    }

    /**
     * The inner function of the HttpKernel.
     * This function will be wrapped with middleware's
     * @param Closure $core the core function
     * @return Closure
     */
    private function createCoreFunction(Closure $core): Closure
    {
        return function ($object) use ($core) {
            return $core($object);
        };
    }

    private function createMiddleware($nextMiddleware, $middleware): Closure
    {
        return function ($object) use ($nextMiddleware, $middleware) {
            return (new $middleware)->handle($object, $nextMiddleware);
        };
    }
}
