<?php

namespace LittleNinja;

class Container implements \ArrayAccess
{

    private $bindings = array();
    private $instances = array();

    public function bind($key, $value, bool $singleton = false)
    {
        $this->bindings[$key] = compact('value', 'singleton');
    }

    public function singleton($key, $value)
    {
        return $this->bind($key, $value, true);
    }

    public function getBinding($key)
    {
        if (!array_key_exists($key, $this->bindings)) {
            return null;
        }

        return $this->bindings[$key];
    }

    public function isSingleton($key)
    {
        $binding = $this->getBinding($key);
        if ($binding === null) {
            return false;
        }

        return $binding['singleton'];
    }

    public function singletonResolved($key)
    {
        return array_key_exists($key, $this->instances);
    }

    public function getSingletonInstance($key)
    {
        return $this->singletonResolved($key) ? $this->instances[$key] : null;
    }

    private function buildDependencies(array $dependencies, array $args, $class)
    {
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

        return $args;
    }

    private function buildObject($class, array $args = array())
    {
        $className = $class['value'];
        $reflector = new \ReflectionClass($className);
        if (!$reflector->isInstantiable()) {
            throw new Exceptions\ClassIsNotInstantiable("Class [$className] is not a resolvable dependency.");
        }

        if ($reflector->getConstructor() !== null) {
            $constructor = $reflector->getConstructor();
            $dependencies = $constructor->getParameters();

            $args = $this->buildDependencies($dependencies, $args, $class);
        }

        $object = $reflector->newInstanceArgs($args);

        return $object;
    }

    private function prepareObject($key, $object)
    {
        if ($this->isSingleton($key)) {
            $this->instances[$key] = $object;
        }

        return $object;
    }

    public function resolve($key, array $args = array())
    {
        $class = $this->getBinding($key);

        if ($class === null) {
            $class = $key;
        }

        if ($this->isSingleton($key) && $this->sigletonResolved($key)) {
            return $this->getSingletonInstance($key);
        }

        $object = $this->buildObject($class, $args);

        return $this->prepareObject($key, $object);
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->bindings);
    }

    public function offsetGet($offset)
    {
        return $this->resolve($offset);
    }

    public function offsetSet($offset, $value)
    {
        return $this->bind($offset, $value);
    }

    public function offsetUnset($offset)
    {
        unset($this->bindings[$offset]);
    }

}
