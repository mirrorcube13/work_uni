<?php

namespace PTS\Core;

use PTS\Controllers\ErrorController;

class Request
{
    private const responseCodes =
        [
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-Status',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            426 => 'Upgrade Required',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            509 => 'Bandwidth Limit Exceeded',
            510 => 'Not Extended'
        ];

    public static function path()
    {
        $path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        if($path) return $path;
        else return '/';
    }

    public static function requestMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public static function back(string $where = '')
    {
        if (empty($where)) $where = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
        
        header('Location: ' . $where);

        return $where;
    }

    private static function convertEmptyStringsToNull(){
        array_walk_recursive($GLOBALS['_' . self::requestMethod()],function (&$var){
            if (is_string($var)){
                if (empty($var) && $var !== "0") $var = null;
                else $var = trim($var);
            }
        });
    }

    public static function error(int $code)
    {
        Request::header($code);
        if ($code === 401) return Request::back('/register');

        $controller = new ErrorController();
        return $controller->errorPage($code);
    }

    public static function getAll() : array
    {
        self::convertEmptyStringsToNull();
        return $GLOBALS['_' . self::requestMethod()];
    }

    public static function header(int $code) : bool
    {
        if (isset(self::responseCodes[$code])){
            header($_SERVER['SERVER_PROTOCOL'] . " {$code} ". self::responseCodes[$code],true, $code);
            return true;
        }
        return false;
    }

    public static function isPost() : bool
    {
        return (self::requestMethod() === "POST");
    }

    public static function isGet() : bool
    {
        return (self::requestMethod() === "GET");
    }
}