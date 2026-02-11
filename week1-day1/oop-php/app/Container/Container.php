<?php

namespace App\Container;

use Reflection;
use ReflectionClass;

class Container
{
    protected array $bindings = [];

    public function bind(string $abstract, string $concrete)
    {
        $this->bindings[$abstract] = $concrete;
    }

    public function make(string $class)
    {
        if (isset($this->bindings[$class])) {
            $class = $this->bindings[$class];
        }

        $reflector = new ReflectionClass($class);

        if (!$reflector->isInstantiable()) {
            throw new \Exception("Class $class is not instantiable");
        }

        $constructor = $reflector->getConstructor();

        if (!$constructor) {
            return new $class;
        }

        $dependencies = [];

        foreach ($constructor->getParameters() as $param) {
            $type = $param->getType();

            if ($type === null) {
                throw new \Exception("Cannot resolve dependency");
            }

            $dependencies[] = $this->make($type->getName());
        }

        return $reflector->newInstanceArgs($dependencies);
    }
}