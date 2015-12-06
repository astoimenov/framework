<?php

namespace LittleNinja;

class Loader
{

    private static $namespaces = array();

    private function __construct()
    {

    }

    private static function loadClass($class)
    {
        foreach (self::$namespaces as $key => $value) {
            if (strpos($class, $key) === 0) {
                $file = str_replace('\\', DIRECTORY_SEPARATOR, $class);
                $file = substr_replace($file, $value, 0, strlen($key));
                $file = realpath($file . '.php');
                if ($file && is_readable($file)) {
                    include $file . '.php';
                } else {
                    throw new \Exception('File could not be included: ' . $file, 500);
                }

                break;
            }
        }
    }

    public static function autoload($class)
    {
        self::loadClass($class);
    }

    public static function registerAutoload()
    {
        spl_autoload_register(['\LittleNinja\Loader', 'autoload']);
    }

    public static function registerNamespace($namespace, $path)
    {
        $namespace = trim($namespace);
        if (strlen($namespace) > 0) {
            if (!$path) {
                throw new \Exception('Invalid path', 500);
            }

            $path = realpath($path);
            if ($path && is_dir($path) && is_readable($path)) {
                self::$namespaces[$namespace . '\\'] = $path . DIRECTORY_SEPARATOR;
            } else {
                throw new \Exception('Namespace directory read error: ' . $path, 500);
            }
        } else {
            throw new \Exception('Invalid namespace: ' . $namespace, 500);
        }
    }

    public static function registerNamespaces(array $namespaces = array())
    {
        if (is_array($namespaces)) {
            foreach ($namespaces as $key => $value) {
                self::registerNamespace($key, $value);
            }
        } else {
            throw new \Exception('Invalid namespaces', 500);
        }
    }

}
