<?php

namespace App\Services;

use App\Services\Container;

abstract class Controller
{

    /**
     * @var mixed|null
     */
    protected $view = null;

    /**
     * @var null|Db\PDO
     */
    protected $pdo = null;

    /**
     * @var null|Container
     */
    protected $container = null;

    /**
     * @var mixed|null
     */
    protected $mailer = null;

    /**
     * @var mixed|null
     */
    protected $message = null;

    public function __construct()
    {
        $this->container = Container::getInstance();

        if (isset($this->container['view']))
            $this->view = $this->container['view'];

        //$this->setConnect();

        $this->run();

    }

    /**
     * run initialize configuration app controllers
     */
    private function run()
    {
        if (property_exists($this, 'layout')) {
            $this->view->setLayout($this->layout);
        }

        if (method_exists($this, 'init')) {
            $this->init();
        }

    }

   /* private function setConnect()
    {
        $database = $this->container['connection'];

        if ($database['driver'] == 'mysql') {
            $dsn = "mysql:host={$database['host']};dbname={$database['database']};charset={$database['charset']}";
            $this->pdo = new Db\PDO($dsn, $database['username'], $database['password']);
        }

        if ($database['driver'] == 'sqlite') {
            $this->pdo = new Db\PDO($database['database']);
        }
    }*/

    private function init(){}

} 