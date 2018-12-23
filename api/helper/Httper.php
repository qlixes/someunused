<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require HELPERPATH . 'Utils.php';

class Httper extends Utils
{
	var $format = array();

	// next update flag will has constanta
	function getResponse($flag, $label, $data = array())
	{
		require __DIR__ . '/Lang.php';

		header("Content-Type: application/json; charset=UTF-8");
		if($flag)
		{
			http_response_code(200);
			$output = array(
				'code' => 200,
				'message' => lang($label),
				'data' => $data
			);
		} else {
			http_response_code(404);
			$output = array(
				'code' => 404,
				'message' => lang($label),
			);
		}
		http_response_code();
		echo json_encode($output, JSON_PRETTY_PRINT);
	}

	function getRequestParams()
	{
		if($_SERVER['REQUEST_METHOD'] === 'POST' || 'post')
		{
			$json = json_decode(file_get_contents('php://input'), true);

			if(!is_array($json))
				$json = array();

			$data = array_merge($_POST, $json
			);
			$final = $this->parser($data);
		}
		else
			$final = $this->parser($_GET);
			// $data = $_GET;

		return $final;
	}

	function getRequestParams2() //with base64 encoder
	{
		// receive decoded data
		$request_body = file_get_contents('php://input');

		$plain_request_body = json_decode((($this->items('use_base64')) ? base64_decode($request_body) : $request_body),true);

		// $plain_login = array('username' => $_SERVER['PHP_AUTH_USER'], 'password' => $_SERVER['PHP_AUTH_PW']);
		$plain_login = array();

		$all_request = (is_array($plain_request_body)) ? array_merge($plain_login, $_GET, $_POST, $plain_request_body) : array_merge($plain_login, $_GET, $_POST);

		return $this->parser($all_request);
	}
}
