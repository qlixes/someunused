<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require HELPERPATH . 'Database.php';

class Models extends Database
{
	var $pdo;
	var $print;
	var $is_empty;
	var $check;

	function __construct()
	{
		try {
		    $this->pdo = new PDO('mysql:host=' . $this->items('hostname') . ';dbname=' . $this->items('database'), $this->items('username'), $this->items('password'));
		    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch(PDOException $e) {
		    echo 'ERROR: ' . $e->getMessage();
		}
	}

	function read($sql, $params = array())
	{
		$stmt = $this->pdo->prepare($sql);
		$result = $stmt->execute($params);

		$this->status = ($result);

		if($stmt->rowCount() > 1 )
			$this->result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		else
			$this->result = $stmt->fetch(PDO::FETCH_ASSOC);

		// $this->status = (!empty($this->result));
		return $this;
	}


	function edit($sql, $params = array())
	{
		$stmt = $this->pdo->prepare($sql);
		$result = $stmt->execute($params);

		$this->status = ($result);

		return $this;
	}

	function status()
	{
		return $this->status;
	}

	function results()
	{
		return $this->result;
	}

	function insertID()
	{
		return $this->pdo->lastInsertId();
	}

	// using fieldname on table
	function alias($field = array())
	{
		require __DIR__ . '/../conf/alias.php';
		$this->check = true;
		$result = '';
		foreach($field as $j => $key)
		{
			$result .= $key . ' as "' . $output[$key] . '"'; // litle hacks for whitespace
			if($j < count($field)-1)
				$result .= ', ';
		}
		$this->print = $result;
		unset($result);
		return $this;
	}

	function show()
	{
		if($this->check)
		{
			$res = $this->print;
			$this->check = false;
		} else
			$res = '*';
		return $res;
	}

	function show_query()
	{
		return $this->query;
	}

	// function alias($field = array())
	// {
	// 	require __DIR__ . '/../conf/alias.php';
	// 	$result = ''; //for resetting
	// 	$status = (!empty($field)); // for resetting
	// 	foreach($field as $j => $key)
	// 	{
	// 		$result .= $key . ' as "' . $output[$key] . '"'; // litle hacks for whitespace
	// 		if($j < count($field)-1)
	// 			$result .= ', ';
	// 	}
	// 	$this->print = $result;
	// 	$this->is_empty = $status;
	// 	unset($result);
	// 	return $this;
	// }

	// function show()
	// {
	// 	$data = ($this->is_empty) ? $this->print : '*';
	// 	unset($this->print);
	// 	return $data;
	// }
}
