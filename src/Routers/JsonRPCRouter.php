<?php

namespace LittleNinja\Routers;

use LittleNinja\App;

class JsonRPCRouter implements IRouter
{

    private $map = array();
    private $requestId = null;
    private $post = array();

    public function __construct()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST' ||
                empty($_SERVER['CONTENT_TYPE']) ||
                $_SERVER['CONTENT_TYPE'] != 'application/json') {
            throw new \Exception('Require JSON request.', 400);
        }
    }

    public function setMethodMaps(array $routes)
    {
        if (is_array($routes)) {
            $this->map = $routes;
        }
    }

    public function getUri()
    {
        if (!is_array($this->map) || count($this->map) === 0) {
            $routes = App::getInstance()->getConfig()->rpcRoutes;
            if (is_array($routes) && count($routes) > 0) {
                $this->map = $routes;
            } else {
                throw new \Exception('RPC Router requires method map.', 500);
            }
        }

        $request = json_decode(file_get_contents('php://input'), true);
        if (!is_array($request) || !isset($request['method'])) {
            throw new \Exception('Require JSON request.', 400);
        } else {
            if ($this->map[$request['method']]) {
                $this->requestId = $request['id'];
                $this->post = $request['params'];

                return $this->map[$request['method']];
            } else {
                throw new \Exception('Method not found.', 501);
            }
        }
    }

    public function getRequestId()
    {
        return $this->requestId;
    }

    public function getPost()
    {
        return $this->post;
    }

}
