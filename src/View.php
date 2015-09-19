<?php

namespace LittleNinja;

class View
{

    private static $instance = null;
    private $viewPath = null;
    private $viewDir = null;
    private $data = array();
    private $extension = '.php';
    private $layoutParts = array();
    private $layoutData = array();

    private function __construct()
    {
        $this->viewPath = App::getInstance()->getConfig()->app['views_path'];
        if ($this->viewPath === null) {
            $this->viewPath = realpath(__DIR__ . '/Views/');
        }
    }

    public function setViewDirectory($path)
    {
        $path = trim($path);
        if ($path) {
            $path = realpath($path) . DIRECTORY_SEPARATOR;
            if (is_dir($path) && is_readable($path)) {
                $this->viewDir = $path;
            } else {
                throw new \Exception('Invalid views path', 500);
            }
        } else {
            throw new \Exception('Views path not specified', 500);
        }
    }

    public function render($name, array $data = array(), $returnAsString = false)
    {
        if (is_array($data)) {
            $this->data = array_merge($this->data, $data);
        }

        if (count($this->layoutParts) > 0) {
            foreach ($this->layoutParts as $key => $value) {
                $r = $this->includeFile($value);
                if ($r) {
                    $this->layoutData[$key] = $r;
                }
            }
        }

        if ($returnAsString) {
            return $this->includeFile($name);
        } else {
            echo $this->includeFile($name);
        }
    }

    public function getLayoutData($name)
    {
        return $this->layoutData[$name];
    }

    public function includeFile($file)
    {
        if ($this->viewDir === null) {
            $this->setViewDirectory($this->viewPath);
        }

        $_fl = $this->viewDir . str_replace('.', DIRECTORY_SEPARATOR, $file) . $this->extension;
        if (file_exists($_fl) && is_readable($_fl)) {
            ob_start();
            include $_fl;
            return ob_get_clean();
        } else {
            throw new \Exception('View ' . $file . ' cannot be included', 500);
        }

        return null;
    }

    public function appendToLayout($key, $template)
    {
        if ($key && $template) {
            $this->layoutParts[$key] = $template;
        } else {
            throw new \Exception('Layout require valid key and template', 500);
        }
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function __get($name)
    {
        return $this->data[$name];
    }

    /**
     * @return \LittleNinja\View
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new View();
        }

        return self::$instance;
    }

}
