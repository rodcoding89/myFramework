<?php

namespace App\Services;
use App\Model\Route;
use Symfony\Component\Yaml\Yaml;

class Router{
    const ROUTES = __DIR__ . '/routes.yaml';
    protected $routes;
    public function __construct()
    {
        var_dump(__DIR__);
        $this->parseRoute();
        $this->routes = new \SplObjectStorage;
    }
    public function parseRoute(){
        $routes = Yaml::parse(file_get_contents( __DIR__ . '/../../config/routes.yaml'));
        
        foreach ($routes as $route) {
            $this->addRoute($route);
            var_dump($route);
        }
    }

    public function addRoute(array $route){
        new Route($route);
        //dispatcher les routes
    }

    public function getRoute($url, $verb = 'get')
    {
        foreach ($this->routes as $route) {
            if ($route->isMatch($url, $verb)) {
                return $route;
            }
        }

        throw new \RuntimeException("bad route exception, getRoute");
    }
}