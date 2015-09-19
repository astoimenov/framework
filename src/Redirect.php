<?php

namespace LittleNinja;

class Redirect
{

    public static function home()
    {
        $app = App::getInstance();
        $config = $app->getConfig();

        header('Location: ' . $config->app['url'] . '/home/index');
        exit;
    }

    public static function to($path)
    {
        $app = App::getInstance();
        $config = $app->getConfig();

        header('Location: ' . $config->app['url'] . $path);
        exit;
    }

}
