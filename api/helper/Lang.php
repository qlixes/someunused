<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function lang($label)
{
	require CONFIGPATH . 'lang.php';

	return $lang[$label];
}
