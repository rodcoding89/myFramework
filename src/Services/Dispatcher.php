<?php

namespace App\Services;

use App\Services\Container;
use App\Services\Request;

class Dispatcher
{

    /**
     * @var null|Container
     */
    private $container = null;

    /**
     * @var null|Container
     */
    protected $json = null;

    /**
     * @var string
     */
    private $content = '';

    /**
     * @var null|Request
     */
    private $request = null;

    /**
     * @var array
     */
    private $params = [];

    /**
     * @var mixed|null
     */
    private $router = null;

    public function __construct(Request $request)
    {
        $this->container = Container::getInstance();

        if (!empty($this->container['router'])) $this->router = $this->container['router'];

        $this->request = $request;
    }

    /**
     * run method return view
     *
     * @throws \Exception
     */
    public function run()
    {
        if (is_null($this->router))
            throw new \RuntimeException('no route');

        try {

            $route = $this->router->getRoute($this->request->getUri(), $this->request->getVerb());

            if ($controller = $route->getController()) $controller = $this->makeController($controller);

            $this->params = ($route->getParams()) ? $route->getParams() : [];

            $this->content = $this->call($controller, $route->getAction());

            $this->send();

        } catch (\RuntimeException $e) {

            $controller = $this->makeController('App\\Controllers\\NotFoundController');
            $this->content = $this->call($controller, 'index');

            $this->send('404 Not Found');
        }
    }

    /**
     * @param $controller
     * @return mixed
     * @throws \Exception
     */
    protected function makeController($controller)
    {

        $controllerClass = sprintf('%s', ucfirst($controller));

        if (!class_exists($controllerClass)) {
            throw new \RuntimeException("Class $controllerClass not found.");
        }

        return new $controllerClass($this->container);
    }

    /**
     * @param $instance
     * @param $method
     * @return mixed
     * @throws \Exception
     */
    protected function call($instance, $method)
    {

        if (!method_exists($instance, $method)) {
            throw new \RuntimeException("Action $method not found");
        }

        return call_user_func_array([$instance, $method], $this->params);
    }

    /**
     * @param string $status
     * @return string
     */
    public function send($status = '200 OK')
    {
        header("HTTP/1.1 $status");
        header("Content-Type: text/html, charset=UTF-8");

        echo (string)$this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->content;
    }

} 