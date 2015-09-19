<?php

namespace LittleNinja;

class Config
{

    private static $instance = null;
    private $configArray = array();
    private $configFolder = null;

    private function __construct()
    {

    }

    public function getConfigFolder()
    {
        return $this->configFolder;
    }

    public function setConfigFolder($folder)
    {
        if (!$folder) {
            throw new \Exception('Empty config folder path: ', 500);
        }

        $configFolder = realpath($folder);
        if ($configFolder != false && is_dir($configFolder) && is_readable($configFolder)) {
            $this->configArray = array();
            $this->configFolder = $configFolder . DIRECTORY_SEPARATOR;
            $namespaces = $this->app['namespaces'];
            if (is_array($namespaces)) {
                Loader::registerNamespaces($namespaces);
            }
        } else {
            throw new \Exception('Config folder read error: ' . $configFolder, 500);
        }
    }

    public function __get($name)
    {
        if (!isset($this->configArray[$name])) {
            $this->includeConfigFile($this->configFolder . $name . '.php');
        }

        if (array_key_exists($name, $this->configArray)) {
            return $this->configArray[$name];
        }

        return null;
    }

    private function includeConfigFile($path)
    {
        if (!$path) {
            throw new \Exception;
        }

        $file = realpath($path);
        if ($file != false && is_file($file) && is_readable($file)) {
            $basename = explode('.php', basename($file))[0];
            $this->configArray[$basename] = include $file;
        } else {
            throw new \Exception('Config file read error: ' . $path, 500);
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Config();
        }

        return self::$instance;
    }

}
