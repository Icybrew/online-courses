<?php


namespace App\Core\Factories;


use App\Core\Data\Data;


/**
 * Class Factory
 * @package App\Core\Factories
 */
class Factory implements IFactory
{


    /**
     * @param string|null $abstract
     * @param array|null $parameters
     * @return mixed|object|null
     * @throws \ReflectionException
     */
    public function make(string $abstract = null, array $parameters = null)
    {
        $reflectionClass = new \ReflectionClass($abstract);

        if (!$reflectionClass->isInstantiable()) {
            return null;
        }

        $constructor = $reflectionClass->getConstructor();

        if (is_null($constructor)) {
            $concrete = new $abstract;
        } else {
            $dependencies = $this->resolveDependencies($constructor->getParameters());
            $concrete = $reflectionClass->newInstanceArgs($dependencies);
        }

        return $concrete;
    }

    /**
     * @param $instance
     * @param $method
     * @param array $parameters
     * @return mixed
     * @throws \ReflectionException
     */
    public function call($instance, $method, array $parameters = [])
    {
        // Checking if received instance instantiated
        if (!is_object($instance)) {
            $instance = $this->make($instance);
        }

        // Reflecting method
        if (!($method instanceof \ReflectionMethod)) {
            $method = new \ReflectionMethod($method);
        }

        // Resolving dependencies
        $dependencies = $this->resolveDependencies($method->getParameters(), $parameters);

        // Returning call
        return call_user_func_array([$instance, $method], $dependencies);
    }

    /**
     * @param array $dependencies
     * @param array $data
     * @return array
     */
    private function resolveDependencies(array $dependencies, array $data = []): array
    {
        $results = [];

        $container = app();

        foreach ($dependencies as $dependency) {
            $type = $dependency->getType();

            if (!is_null($type) && $container->has($type->getName())) {
                $results[] = $container->get($type->getName());
            } else {
                $results[] = array_pop($data);
            }
        }

        return $results;
    }
}
