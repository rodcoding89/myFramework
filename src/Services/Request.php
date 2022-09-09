<?php

namespace App\Services;

class Request
{

    /**
     * Hold URI
     * @var string
     */
    protected $uri = null;

    /**
     * Hold request method
     * @var string
     */
    protected $method = null;

    /**
     * Hold request headers
     * @var array
     */
    protected $headers = [];

    /**
     * Hold request's parameters
     * @var [type]
     */
    protected $params = [];

    public function __construct()
    {
        $this->uri = filter_var($_SERVER["REQUEST_URI"], FILTER_SANITIZE_STRING);
        $this->method = $_SERVER["REQUEST_METHOD"];
        $this->headers = apache_request_headers();

        if ($this->isPost()) {
            $this->params = array_merge($this->params, $_POST);
        }
    }

    /**
     * Get the request's URI
     *
     * @return string The URI
     */
    public function getURI()
    {
        return $this->uri;
    }

    /**
     * Get the request's headers
     *
     * @return array An array with headers
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Get params of the request
     *
     * @return array An array with the params
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Say if the request is GET
     *
     * @return boolean
     */
    public function isGet()
    {
        return ($this->method === "GET");
    }

    /**
     * Say if the request is POST
     *
     * @return boolean
     */
    public function isPost()
    {
        return ($this->method === "POST");
    }

    public function getVerb()
    {
        if ($this->isPost()) {

            if (!empty($_POST['_method'])) {
                switch (strtolower($_POST['_method'])) {
                    case 'put':
                        $this->method = 'PUT';
                        break;
                    case 'patch':
                        $this->method = 'PATCH';
                        break;
                    case 'delete':
                        $this->method = 'DELETE';
                        break;
                    default:
                        $this->method = 'POST';
                }
            }
        }

        return $this->method;
    }

}