<?php

namespace App\Services;

class Container implements \ArrayAccess
{

    protected $p = [];
    protected $alias = [];

    /**
     * @var Container|null
     */
    private static ?Container $_instance = null;

    /**
     * @param mixed $k
     * @param mixed $v
     */
    public function offsetSet($k, $v) :void
    {
        if (isset($this->p[$k])) {
            throw new \RuntimeException(sprintf('Cannot override frozen service "%s".', $k));
        }

        $this->p[$k] = $v;
    }

    /**
     * @param mixed $k
     * @return mixed
     */
    public function offsetGet($k)
    {

        if (!isset($this->p[$k]))
            throw new \InvalidArgumentException('unknow value:' . $k);

        if (is_callable($this->p[$k]))
            return $this->p[$k]($this);

        return $this->p[$k];
    }

    /**
     * @param mixed $id
     * @return bool
     */
    public function offsetExists($id) :bool
    {
        return isset($this->p[$id]);
    }

    /**
     * @param mixed $id
     */
    public function offsetUnset($id) :void
    {
        if (isset($this->p[$id])) {
            unset($this->p[$id]);
        }
    }

    /**
     * @param callable $callable
     * @return callable
     */
    public function asShared(\Closure $callable)
    {

        return function ($c) use ($callable) {
            static $o = null;
            if (is_null($o)) {
                $o = $callable($c);
            }

            return $o;
        };
    }

    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

}