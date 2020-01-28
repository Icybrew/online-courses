<?php

namespace App\Core\Routing;


/**
 * Class URL
 * @package App\Core\Routing
 */
class URL
{
    protected $method;
    protected $_url_unprocessed;
    protected $_url_processed;
    protected $_url_exploded;


    /**
     * URL constructor.
     */
    public function __construct()
    {
        $this->method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];
        $this->_url_unprocessed = isset($_GET['url']) ? $_GET['url'] : '';
        $this->_url_processed = $this->processURL();
        $this->_url_exploded = explode('/', $this->_url_processed);
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return strtoupper($this->method);
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->_url_processed;
    }

    public function getParam($index)
    {
        return isset($this->_url_exploded[$index]) ? $this->_url_exploded[$index] : null;
    }

    /**
     * @return string
     */
    public function getBaseURL(): string
    {
        return "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

    /**
     * @return string
     */
    private function processURL(): string
    {
        $url = $this->_url_unprocessed;

        if (isset($url)) {
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = rtrim($url, '/');

            return $url;
        } else {
            return '/';
        }
    }

    public function is($name)
    {
        // TODO fix to work with url variables
        return app(Router::class)->getRoute()->getName() == $name;
    }
}
