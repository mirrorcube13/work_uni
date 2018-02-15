<?php

namespace PTS\Core;

class Route implements \ArrayAccess
{
    private $response;

    private static $params = [];

    private static $instance;


    protected static $routes = [
        'GET' => [],
        'POST' => []
    ];

    public static function run()
    {
        $router = new self;
        self::$instance = $router;
        require_once "route_list.php";

        return $router;
    }

    public function get($url, $controllerMethod)
    {
        $this->saveRoute(self::$routes['GET'], $url, $controllerMethod);
        return $this;
    }

    public function post($url, $controllerMethod)
    {
        $this->saveRoute(self::$routes['POST'], $url, $controllerMethod);
        return $this;
    }

    private function saveRoute(&$method, $url, $controllerMethod)
    {
        if (preg_match_all('@{(?P<keys>\w+)}@', $url, $params)){
            foreach ($params[0] as $param){
                $url = str_replace($param, '', $url);
            }
            $url = trim($url, '/');
            foreach ($params['keys'] as $key){
                $method[$url]['params'][] = [ 'key' => $key, 'req' => false]; //true means required
            }
        }
        $method[$url]['controller'] = $controllerMethod;
        $method[$url]['auth'] = false;
    }

    public function processRequest()
    {
        $isRoute = false;

        $path = Request::path();
        $method = Request::requestMethod();

        if(array_key_exists($path, self::$routes[$method])) {
            $isRoute = true;
        }else{
            $routeParts = explode('/', $path);
            if (isset(self::$routes[$method][$routeParts[0]])){
                $path = array_shift($routeParts);

                $nOfParts = count($routeParts);
                for($i = 0; $i < $nOfParts; $i++){
                    if (isset(self::$routes[$method][$path]['params'][$i]))
                    self::$params[self::$routes[$method][$path]['params'][$i]['key']] = $routeParts[$i];
                }

                $isRoute = true;
            }
        }
        if ($isRoute){
            $this->response = $this->callController(... explode('@', self::$routes[$method][$path]['controller']));
        }else {
            $this->response = Request::error(404);
        }
        return $this;

    }

    protected function callController($class, $method)
    {
        $class = 'PTS\Controllers\\' . $class;
        return (new $class)->$method(... array_values(self::params()));
    }

    public function processResponse()
    {
        if ( isset($this->response) ) {
            $response = null;

            if ($this->response instanceof View) $response = $this->response->show();
            elseif (is_array($this->response)) $response = json_encode($this->response);

            if (!is_null($response)) echo $response;
        }
    }

    public function params(){
        return self::$params;
    }

    public function offsetExists($offset)
    {
        if (isset(self::$params[$offset])) return true;
        else return false;
    }

    public function offsetGet($offset)
    {
        return self::$params[$offset];
    }

    public function offsetSet($offset, $value){}

    public function offsetUnset($offset){}

    public static function getInstance()
    {
        return self::$instance;
    }

    protected function __construct(){}
    protected function __clone(){}
}
