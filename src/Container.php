<?php

namespace LittleNinja;

class Container {

    private $bindings = array();

    public function bind($key, $value) {
        $this->bindings[$key] = $value;
    }

    public function getBinding($key) {
        if (!array_key_exists($key, $this->bindings)) {
            return null;
        }

        return $this->bindings[$key];
    }

    public function resolve($key, array $args = array()) {
        $class = $this->getBinding($key);

        if ($class === null) {
            $class = $key;
        }

        return $this->buildObject($class, $args);
    }

    private function buildObject($className, array $args = array()) {
        $reflector = new \ReflectionClass($className);
        if (!$reflector->isInstantiable()) {
            throw new Exceptions\ClassIsNotInstantiable("Class [$className] is not a resolvable dependency.");
        }

        if ($reflector->getConstructor() !== null) {
            $constructor = $reflector->getConstructor();
            $dependencies = $constructor->getParameters();

            foreach ($dependencies as $dependency) {
                if ($dependency->isOptional() || $dependency->isArray()) {
                    continue;
                }

                $class = $dependency->getClass();
                if ($class === null) {
                    continue;
                }

                if (get_class($this) === $class->name) {
                    array_unshift($args, $this);
                    continue;
                }

                array_unshift($args, $this->resolve($class->name));
            }
        }

        $object = $reflector->newInstanceArgs($args);

        return $object;
    }

}
