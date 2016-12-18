<?php

namespace LittleNinja;

class Redirect
{
    public static function home()
    {
        $config = App::getInstance()->getConfig();

        header('Location: '.$config->app['url'].'/home/index');
        exit;
    }

    public static function to($path)
    {
        $config = App::getInstance()->getConfig();

        header('Location: '.$config->app['url'].$path);
        exit;
    }
}
