<?php

define('BASEPATH', __DIR__ . '/');
define('COREPATH', __DIR__ . '/core/');
define('CONTROLLERPATH', __DIR__ . '/controllers/');
define('MODELPATH', __DIR__ . '/models/');
define('HELPERPATH', __DIR__ . '/helper/');
define('CONFIGPATH', __DIR__ . '/conf/');

require COREPATH . 'Base.php';

$uri_path = str_replace($_SERVER['SCRIPT_NAME']. '/', '', $_SERVER['PHP_SELF']);

$shortcut = ($uri_path === $_SERVER['SCRIPT_NAME']) ? 'errors/index' : $uri_path;

$app = new Base();
$app->router($shortcut, $_GET);
