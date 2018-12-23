<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require HELPERPATH . 'Httper.php';

interface IController
{
    function index();
}

class Controller extends Httper
{
    static $controller = array();
    var $users;

    function __construct()
    {
        $this->users = $this->loadClass('users_model', 'models');
    }

    function loadClass($class, $path)
    {
        $controllers = ucfirst(($class && file_exists(BASEPATH .  $path . '/' . ucfirst($class) . '.php')) ? ($class) : ('errors'));

        require BASEPATH . $path . '/' . $controllers . '.php';

        if(empty(self::$controller[$controllers]))
            self::$controller[$controllers] = new $controllers();

        return self::$controller[$controllers];
    }
}
