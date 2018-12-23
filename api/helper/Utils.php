<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require CONFIGPATH . 'const.php';
require HELPERPATH . 'Config.php';

class Utils extends Config
{
	function get_ip()
	{
		return $_SERVER['REMOTE_ADDR'];
	}

	function get_agent()
	{
		return $_SERVER['HTTP_USER_AGENT'];
	}

	// Thank you for Mr.Pandu
	function formatdatemon( $strdate = null, $time = false, $dateformat = 'Y-m-d')
	{
	    if(empty($strdate))
	        $strdate = 'now';
	    else if($strdate == 'first')
	        $strdate = 'first day of this month';
	    else if($strdate == 'last')
	        $strdate = 'last day of this month';

	    if($time === true)
	        $dateformat .= " H:i:s";

	    // $strdate = date($dateformat, strtotime($strdate));
	    // if(date('Y', strtotime($strdate)) < MIN_YEAR)
	    //     return null;
	    // else
	    //     return $strdate;

	    $local = new DateTime($strdate, new DateTimeZone(TIMEZONE_STR));

	    return $local->format($dateformat);
	}

	// need handler for empty int, string
	function filter_used($data = array(), $filter = array())
	{
		$result = array();
		foreach($filter as $i => $key)
			if(!empty($data[$key]))
				$result[$key] = $data[$key];
			else
				if(isset($data[$key]) && ($data[$key] == 0 || $data[$key] == ''))
					$result[$key] = $data[$key];
				else
					$missing[] = $key;
		$flag = (empty($missing));
		return array($flag, $result);
	}

	function filter_default($data = array(), $default = array())
	{
		$result = array();
		foreach($default as $key => $val)
		{
			if(!isset($data[$key]))
				$result[$key] = $val;
		}

		return array_merge($data, $result);
	}

	function hash_password($password)
	{
		return sha1($password);
	}

	function input($label)
	{
		require CONFIGPATH . 'alias.php';

		if(!empty($input[$label]))
			return $input[$label];
	}

	function output($label)
	{
		require CONFIGPATH . 'alias.php';

		if(empty($output[$label]))
			$data = $label;
		else
			$data = $output[$label];

		return $data;
	}

	// parser params input to translate into alias input
	function parser($params = array())
	{
		$result = array();
		foreach($params as $key => $value)
			$result[$this->input($key)] = $value;

		return $result;
	}
}
