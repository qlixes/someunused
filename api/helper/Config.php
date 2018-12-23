<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Config
{
	function items($key)
	{
		require CONFIGPATH . 'config.php';
		
		return $config[$key];
	}
}
