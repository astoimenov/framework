<?php

namespace LittleNinja;

class Controller
{

    /**
     * @var \LittleNinja\App
     */
    protected $app;

    /**
     * @var \LittleNinja\View
     */
    protected $view;

    /**
     * @var \LittleNinja\Config
     */
    protected $config;

    /**
     * @var \LittleNinja\InputData
     */
    protected $inputData;

    public function __construct()
    {
        $this->app = App::getInstance();
        $this->view = View::getInstance();
        $this->config = $this->app->getConfig();
        $this->inputData = InputData::getInstance();
    }

}
