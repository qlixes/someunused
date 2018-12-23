<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Handle Core Controller
 * @var [type]
 */
class Base
{
    static $controller = array();
    var $params = array();

    /**
     * [router description]
     * @param  [type] $index  'site/index'
     * @param  array  $params params with $_GET
     * @return [type]         [description]
     */
    function router($shortcut, $params = array())
    {
        $uri_format = array('controller', 'methods');

        //could add again
        // switch($_SERVER['REQUEST_METHOD']){
        //     case 'POST' || 'post' || 'Post' :
        //         $this->params = array_merge($this->params, $this->filter_data(json_decode(file_get_contents('php://input', true))), $_POST);
        //     case 'GET' || 'get' || 'Get' :
        //         $this->params = array_merge($this->params, $this->filter_data($_GET));
        // }

        $uri = explode('/', $shortcut);

        if(count($uri) === 1)
            $uri[1] = 'index';

        foreach($uri as $i => $value)
            if(!empty($uri_format[$i]))
                $format[$uri_format[$i]] = $value;

        $oController = $this->loadController($format['controller']);

        $is_methods = method_exists($oController, $format['methods']);

        $format['methods'] = ($is_methods) ? $format['methods'] : 'index';

        if($params)
            $objects = $oController->{call_user_func_array($format['methods'], $params)};
        else
            $objects = $oController->{$format['methods']}();

        return $objects;
    }

    function loadController($controller)
    {
        $controllers = ucfirst(($controller && file_exists(CONTROLLERPATH . ucfirst($controller) . '.php')) ? ($controller) : ('errors'));

        require CONTROLLERPATH . $controllers . '.php';

        if(empty(self::$controller[$controllers]))
            self::$controller[$controllers] = new $controllers();

        return self::$controller[$controllers];
    }
}
