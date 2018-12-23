<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require HELPERPATH . 'Models.php';

class Users_model extends Models
{
	function selectUser($params = array())
	{
		$this->query = sprintf('select %s from master_user where username = :username', $this->show());

		if(!empty($params['password']))
			$this->query .= ' and password = :password';
		if(!empty($params['flag_login']))
			$this->query .= ' and flag_login = :flag_login';

		$this->query .= ';'; //end sql

		$sql = $this->read($this->query, $params);

		return array($sql->status(), $sql->results());
	}

	function upLastCheckIn($params = array())
	{
		// $filter = $this->filter_used($params, array('lastcheckin_datetime','lastcheckin_ipaddress','lastcheckin_useragent', 'lastcheckin_location', 'username'));
		$this->query = 'update master_user set lastcheckin_datetime = :lastcheckin_datetime, lastcheckin_ipaddress = :lastcheckin_ipaddress, lastcheckin_useragent = :lastcheckin_useragent, lastcheckin_location = :lastcheckin_location, flag_login = :flag_login where username = :username;';

		$sql = $this->edit($this->query, $params);

		return array($sql->status());
	}

	function upLastLogin($params = array())
	{
		// $filter = $this->filter_used($params, array('lastlogin_datetime', 'lastlogin_ipaddress', 'lastlogin_useragent','username', 'flag_login'));
		$this->query = 'update master_user set lastlogin_datetime = :lastlogin_datetime, lastlogin_ipaddress = :lastlogin_ipaddress, lastlogin_useragent = :lastlogin_useragent, flag_login = :flag_login where username = :username;';

		$sql = $this->edit($this->query, $params);

		return array($sql->status());
	}

	function upLastLogout($params = array())
	{
		$this->query = 'update master_user set flag_login = :flag_login where username = :username;';

		$sql = $this->edit($this->query, $params);

		return array($sql->status());
	}

	function addHistory($params = array())
	{
		// $filter = $this->filter_used($params, array('username','password','datetime','location','ipaddress','useragent','note','flag'));
		$this->query = 'insert into  data_checkin(username, datetime, location, ipaddress, useragent, note, flag) values (:username, :datetime, :location, :ipaddress, :useragent, :note, :flag);';

		$sql = $this->edit($this->query, $params);

		return array($sql->status(), $this->insertID());
	}

	function selectArea($params = array())
	{
		// $filter = $this->filter_used($params, array('lattitude','longitude'));
		// $this->query = sprintf('select %s from master_location where ((:longitude -longitude_min) >= 0 and (longitude_max - :longitude) >= 0) and ((:latitude-latitude_min) <= 0 and (latitude_max - (:latitude)) <= 0);', $this->show());
		$this->query = sprintf('select %s from master_location WHERE latitude_min >= :latitude AND latitude_max <= :latitude AND longitude_min <= :longitude AND longitude_max >= :longitude;', $this->show());

		$sql = $this->read($this->query, $params);

		return array($sql->status(), $sql->results());
	}
}
