<?php

namespace App\Model;

class Route {
     /**
     *
     * @var string
     */
    protected $controller;

    /**
     *
     * @var string
     */
    protected $action;

    /**
     *
     * @var NULL | array
     */
    protected $params = null;

    /**
     *
     * @var Object Routable
     */
    protected $route;

    /**
     * name of route
     *
     * @var string
     */
    protected $name;

    /**
     * @var array string rest actions
     */
    protected $actionRest = ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'];

    /**
     * @var array guarded actions rests
     */
    protected $guardedRestAction = [];

    /**
     * @var string resource name REST
     */
    protected $resource;

    /**
     * @var string verb REST
     */
    protected $verb;

    protected $routeName = null;

    public function __construct(array $route)
    {
        $this->route = $route;

        $connect = $route['connect'];

        if (empty($connect)) {
            throw new \RuntimeException('Bad syntax connect.');
        }

        $this->name = $connect;

        $this->setConnect($connect);
    }

    /**
     * Setter of paramaters route
     *
     * @param array $m
     * @return NULL | array
     */
    public function setParams($m)
    {
        if (empty($this->route['params'])) {
            return;
        }

        $params = explode(',', $this->route['params']);
        foreach ($params as $p) {
            $p = trim($p);
            $this->params[$p] = $m[$p];
        }
    }

    /**
     * set params resource
     *
     * @param array $m
     */
    public function setParamsREST($m)
    {
        $this->params['id'] = (empty($m['id'])) ?: $m['id'];

        if ($this->verb == 'GET') {
            $action = (!empty($m['create'])) ? 'create' : (!empty($m['edit']) ? 'edit' : ((!empty($m['id']) ? 'show' : 'index')));

            $this->routeName = $action;
        }

        if ($this->verb == 'POST') {
            $this->routeName = 'store';
        }

        if ($this->verb == 'PUT' || $this->verb == 'PATCH') {
            $this->routeName = 'update';
        }

        if ($this->verb == 'DELETE') {
            $this->routeName = 'destroy';
        }

    }

    /**
     *
     *  two string separated by : to connect by order controller and action
     *
     * @param array $connect
     * @throws \RuntimeException
     */
    public function setConnect($connect)
    {
        if (!empty($this->route['resource'])) {
            $this->setConnectREST($this->route['resource']);

            return;
        }

        $c = explode(':', $connect);
        if (count($c) != 2) {
            throw new \RuntimeException('Bad syntax connect.');
        }

        list($this->controller, $this->action) = $c;
    }

    /**
     * set action rest routes getGuardedRestAction array
     *
     * @param string $resource
     */
    protected function setConnectREST($resource)
    {
        if (empty($this->route['action'])) throw new \RuntimeException('Bad api action.');
        if (empty($this->route['connect'])) throw new \RuntimeException('Bad api connect.');

        $actions = explode(':', $this->route['action']);

        $this->resource = (string)$resource;

        $this->controller = $this->route['connect'];

        if ($actions[0] == '*') $this->guardedRestAction = $this->actionRest;
        else  $this->guardedRestAction = array_intersect($actions, $this->actionRest);

    }

    public function getController()
    {
        return $this->controller;
    }

    public function getAction()
    {
        if (!empty($this->guardedRestAction) && in_array($this->routeName, $this->guardedRestAction)) {

            return $this->routeName;
        }

        return $this->action;
    }

    public function getParams()
    {
        if (is_null($this->params)) return null;

        foreach ($this->params as $value) $params[] = $value;

        return $params;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array action rest
     */
    public function getGuardedRestAction()
    {
        return $this->guardedRestAction;
    }

    /**
     * @return string verb of action REST
     */
    public function getVerb()
    {
        return $this->verb;
    }

    /**
     *  url match with pattern or REST url
     *
     * @param string $url
     * @param string $verb REST
     * @return boolean
     */
    public function isMatch($url, $verb)
    {
        if (!empty($this->guardedRestAction)) {

            if (preg_match('/^\/?' . $this->route['resource'] . '\/?(?P<create>create)?\/?(?P<id>[0-9]+)?\/?(?P<edit>edit)?$/', $url, $m)) {

                $this->verb = (string)$verb;

                $this->setParamsREST($m);

                return true;
            }

            return false;

        }

        if (preg_match('/^' . $this->route['pattern'] . '$/', $url, $m)) {
            $this->setParams($m);

            return true;
        } else {
            return false;
        }
    }
}