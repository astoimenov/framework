<?php

namespace LittleNinja;

class App {

    private static $instance = null;
    private $config = null;
    private $configFolder;
    private $frontController = null;
    private $router = null;
    private $dbConnections = array();
    private $session = null;

    private function __construct() {
        set_exception_handler(array($this, 'exceptionHandler'));

        include_once 'Loader.php';
        Loader::registerAutoload();

        $this->config = Config::getInstance();
        if ($this->config->getConfigFolder() === null) {
            $this->setConfigFolder('../config');
        }
    }

    public function run() {
        if ($this->config->getConfigFolder() === null) {
            $this->setConfigFolder('../config');
        }

        $this->frontController = FrontController::getInstance();
        if ($this->router instanceof Routers\IRouter) {
            $this->frontController->setRouter($this->router);
        } elseif ($this->router === 'JsonRPCRouter') {
            // $this->frontController->setRouter(new Routers\JsonRPCRouter());
        } elseif ($this->router === 'CLIRouter') {
            // $this->frontController->setRouter(new Routers\CLIRouter());
        } else {
            $this->frontController->setRouter(new Routers\DefaultRouter());
        }

        $sess = $this->config->session;
        if ($sess['autostart']) {
            if ($sess['type'] === 'native') {
                $session = new Sessions\NativeSession(
                    $sess['name'], $sess['lifetime'], $sess['path'], $sess['domain'], $sess['secure']
                );
            }

            $this->setSession($session);
        }

        $this->frontController->dispatch();
    }

    /**
     * @param \LittleNinja\Sessions\ISession $session
     */
    public function setSession(Sessions\ISession $session) {
        $this->session = $session;
    }

    /**
     * @return \LittleNinja\Sessions\ISession
     */
    public function getSession() {
        return $this->session;
    }

    public function setConfigFolder($path) {
        $this->config->setConfigFolder($path);

        return $this;
    }

    public function getConfigFolder() {
        return $this->configFolder;
    }

    public function getRouter() {
        return $this->router;
    }

    public function setRouter($router) {
        $this->router = $router;

        return $this;
    }

    /**
     * @return \LittleNinja\Config
     */
    public function getConfig() {
        return $this->config;
    }

    public function getDBConnection($connection = 'default') {
        if (!$connection) {
            throw new \Exception('No connection indentifier provided', 500);
        }

        if ($this->dbConnections[$connection]) {
            return $this->dbConnections[$connection];
        }

        $cnf = $this->getConfig()->db;
        if (!$cnf[$connection]) {
            throw new \Exception('No valid conection indentifier provided', 500);
        }

        $conCnf = $cnf[$connection];
        $dbCon = new \PDO($conCnf['connection_uri'], $conCnf['username'], $conCnf['password'], $conCnf['pdo_options']);
        /* $dbCon = mysqli_connect($conCnf['host'], $conCnf['user'], $conCnf['pass'], $conCnf['name']);
          if (mysqli_connect_errno()) {
          throw new \Exception('Database connection not established', 503);
          }


          if (!$dbCon->set_charset('utf8')) {
          throw new \Exception('Error loading character set utf8', 503);
          } */

        $this->dbConnections[$connection] = $dbCon;

        return $dbCon;
    }

    /**
     * @return \LittleNinja\App
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new App();
        }

        return self::$instance;
    }

    public function exceptionHandler(\Exception $ex) {
        if ($this->config && $this->config->app['debug'] == true) {
            $whoops = new \Whoops\Run();
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
            $whoops->register();
        } else {
            $this->displayError($ex->getCode());
        }
    }

    public function displayError($errorCode) {
        try {
            $view = View::getInstance();
            $view->render('errors.' . $errorCode);
        } catch (\Exception $ex) {
            Common::headerStatus($errorCode);
            echo '<h1>Error: ' . $errorCode . '</h1>';
            exit();
        }
    }

    public function __destruct() {
        if ($this->session !== null) {
            $this->session->saveSession();
        }
    }

}
