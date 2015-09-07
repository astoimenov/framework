<?php

namespace LittleNinja\Routers;

use LittleNinja\App;

class DefaultRouter implements IRouter {

    public function getUri() {
        $app = App::getInstance();
        $config = $app->getConfig();
        $request = $_SERVER['SERVER_NAME'] . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $requestHome = $config->app['root_path'] . '/';

        if (!empty($request)) {
            if (strpos($request, $requestHome) === 0) {
                $request = substr($request, strlen($requestHome));

                return $request;
            }
        }

        return null;
    }

    public function getPost() {
        return $_POST;
    }

}
