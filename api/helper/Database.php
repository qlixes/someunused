<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Database
{
	function items($key)
	{
		require CONFIGPATH . 'database.php';

		return $config[$key];
	}
}
