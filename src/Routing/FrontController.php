<?php

namespace LittleNinja;

class FrontController
{
    private static $instance = null;
    private $namespace = null;
    private $controller = null;
    private $action = null;
    private $router = null;

    private function __construct()
    {
    }

    /**
     * @return \LittleNinja\Routers\IRouter
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @param \LittleNinja\Routers\IRouter $router
     *
     * @return \LittleNinja\FrontController
     */
    public function setRouter(Routers\IRouter $router)
    {
        $this->router = $router;

        return $this;
    }

    public function dispatch()
    {
        if ($this->router === null) {
            throw new \Exception('No valid router found', 500);
        }

        $uri = $this->router->getUri();
        $routes = App::getInstance()->getConfig()->routes;
        $rc = null;
        if (is_array($routes) && count($routes) > 0) {
            foreach ($routes as $key => $value) {
                if (stripos($uri, $key) === 0 &&
                        ($uri === $key || stripos($uri, $key.'/') === 0) &&
                        $value['namespace']
                ) {
                    $this->namespace = $value['namespace'];
                    $uri = substr($uri, strlen($key) + 1);
                    $rc = $value;
                    break;
                }
            }
        } else {
            throw new \Exception('Default routes missing', 500);
        }

        $defaultRoute = $routes['*'];
        if ($this->namespace === null && $defaultRoute['namespace']) {
            $this->namespace = $defaultRoute['namespace'];
            $rc = $defaultRoute;
        } elseif ($this->namespace === null && !$defaultRoute['namespace']) {
            throw new \Exception('Default namespace missing', 500);
        }

        $input = InputData::getInstance();
        $params = explode('/', strtolower($uri));
        if ($params[0]) {
            $this->controller = ucfirst($params[0]).'Controller';
            $currentController = $params[0];
            if ($params[1]) {
                $this->action = $params[1];
                unset($params[0], $params[1]);
                $input->setGet(array_values($params));
            } else {
                $this->action = $this->getDefaultAction();
            }

            if (is_array($rc) && $rc['controllers']) {
                if (isset($rc['controllers'][$currentController]['actions'][$this->action])) {
                    $this->action = $rc['controllers'][$currentController]['actions'][$this->action];
                }

                if (isset($rc['controllers'][$currentController]['uses'])) {
                    $this->controller = $rc['controllers'][$currentController]['uses'];
                }
            }
        } else {
            $this->action = $this->getDefaultAction();
            $this->controller = $this->getDefaultController();
        }

        $input->setPost($this->router->getPost());

        $c = $this->namespace.'\\'.$this->controller;
        $newController = new $c();
        $newController->{$this->action}();
    }

    private static function getDefaultController()
    {
        $controller = App::getInstance()->getConfig()->defaults['controller'];

        return $controller ?? 'Home';
    }

    private static function getDefaultAction()
    {
        $action = App::getInstance()->getConfig()->defaults['action'];

        return $action ?? 'index';
    }

    /**
     * @return \LittleNinja\FrontController
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
