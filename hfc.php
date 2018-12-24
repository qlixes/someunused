<?php
define('API_KEY', '');
define('FLAG_LOGIN', 1);
define('FLAG_DEFAULT', 0);
define('FLAG_CHECKIN', 2);
define('MIN_YEAR', 2000);

define('DBHOST','localhost');
define('DBUSER', 'root');
define('DBPASS','');
define('DBNAME', 'api');

define('API_INVALID_CREDENTIALS', 'Incorrect login');
define('API_INVALID_ACCESS', 'Access forbidden');
define('API_SUCCESS_LOGIN', 'Login success');
define('API_SUCCESS_LOGOUT', 'logout success');
define('API_SUCCESS_CHECKIN', 'Checkin success');
define('API_MISSING_PARAMETER', 'Missing parameter');
define('API_LOCATION_UNKNOWN', 'Location unknown');

class Helper
{
    var $pdo;
    var $field;
    var $array;

    function __construct()
    {
		try {
		    $this->pdo = new PDO('mysql:host=' . DBHOST . ';dbname=' . DBNAME, DBUSER, DBPASS);
		    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch(PDOException $e) {
		    die('ERROR: ' . $e->getMessage());
		}
    }

    function select($query, $params = array())
    {
        $stmt = $this->pdo->prepare($query);
        $result = $stmt->execute($params);

        return ($stmt->rowCount() > 1) ? $stmt->fetchAll(PDO::FETCH_ASSOC) : $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function update($query, $params = array())
    {
        $stmt = $this->pdo->prepare($query);
        $result = $stmt->execute($params);

        return ($result);
    }

    function field($param = array())
    {
        $this->field = '';
        if(is_array($param))
            $this->field .= implode(',', $param);
        return $this;
    }

    function isPointerExist($params = array())
    {
        return $this->select(sprintf('select %s from master_location WHERE latitude_min >= :latitude AND latitude_max <= :latitude AND longitude_min <= :longitude AND longitude_max >= :longitude;', $this->field), $this->filter($params, array('longitude','latitude')));
    }

    function formatdatemon( $strdate = null, $time = true, $dateformat = 'd-M-Y')
    {
        if(empty($strdate))
            $strdate = 'now';
        else if($strdate == 'first')
            $strdate = 'first day of this month';
        else if($strdate == 'last')
            $strdate = 'last day of this month';

        if($time === true)
            $dateformat .= " H:i:s";

        $strdate = date($dateformat, strtotime($strdate));
        if(date('Y', strtotime($strdate)) < MIN_YEAR)
            return null;
        else
            return $strdate;
    }

    function hash_password($password)
    {
        return sha1($password);
    }

    function isUserExist($params = array())
    {
        $user = sprintf('select %s from master_user where ', $this->field);
        $filter = array();
        if(!empty($params['username']))
        {
            $user .= 'username= :username';
            $filter[] = 'username';
        }
        if(!empty($params['password']))
        {
            $user .= ' and password= :password';
            $filter[] = 'password';
        }
        if(!empty($params['flag_login']))
        {
            $user .= ' and flag_login= :flag_login';
            $filter[] = 'flag_login';
        }

        $user .= ';';
        return $this->select($user, $this->filter($params, $filter));
    }

    function updateUser($params = array())
    {
        $user = 'update master_user set ';
        $filter = array();
        $flag = 0;
        if(!empty($params['lastlogin_datetime']) && !empty($params['lastlogin_ipaddress']) && !empty($params['lastlogin_useragent']))
        {
            $user .= 'lastlogin_datetime= :lastlogin_datetime, lastlogin_ipaddress= :lastlogin_ipaddress, lastlogin_useragent= :lastlogin_useragent';
            $filter = array_merge($filter, array('lastlogin_datetime','lastlogin_ipaddress','lastlogin_useragent'));
            $flag = 1;
        }

        if(!empty($params['lastcheckin_datetime']) && !empty($params['lastcheckin_ipaddress']) && !empty($params['lastcheckin_useragent']) && !empty($params['lastcheckin_location']))
        {
            if($flag == 1) $user .= ',';
            $user .= 'lastcheckin_datetime= :lastcheckin_datetime, lastcheckin_ipaddress= :lastcheckin_ipaddress, lastcheckin_useragent= :lastcheckin_useragent, lastcheckin_location= :lastcheckin_location';
            $filter = array_merge($filter, array('lastcheckin_datetime','lastcheckin_ipaddress','lastcheckin_useragent','lastcheckin_location'));
            $flag = 1;
        }

        if(!empty($params['note']) && !empty($params['flag']))
        {
            if($flag == 1) $user .= ',';
            $user .= 'note= :note, flag= :flag';
            $filter = array_merge($filter, array('note','flag'));
        }

        if($params['flag_login'] >= 0)
        {
            if($flag == 1) $user .= ',';
            $user .= 'flag_login= :flag_login';
            $filter = array_merge($filter, array('flag_login'));
        }
        $user .= ' where username= :username;';
        $filter = array_merge($filter, array('username')); // manual merge for field username

        return $this->update($user, $this->filter($params, $filter));
    }

    function addHistory($params = array())
    {
        return $this->update('insert into data_checkin(username, datetime, ipaddress, useragent, location, longitude, latitude, note, flag) values (:username, :datetime, :ipaddress, :useragent, :location, :longitude, :latitude, :note, :flag);', $this->filter($params, array('username','datetime','ipaddress','useragent','location', 'longitude' , 'latitude','note','flag')));
    }

    function logs($label_datetime, $label_ipaddress, $label_useragent)
    {
        return array(
            // $label_datetime => date('Y-m-d H:i:s'),
            $label_datetime => $this->formatdatemon(),
            $label_ipaddress => $_SERVER['REMOTE_ADDR'],
            $label_useragent => $_SERVER['HTTP_USER_AGENT']
        );
    }

    function _parse_json_array($string)
    {
        $check = @json_decode($string, true);
        return (!$check && json_last_error() !== JSON_ERROR_NONE) ? array() : $check;
    }

    // function _parse_json_array($string)
    // {
    //     $check = @json_decode($string, true);
    //     if(!$check && json_last_error() !== JSON_ERROR_NONE)
    //         die('Failed json_decode');
    //     return  $check;
    // }

    function request()
    {
        // $params = $this->_parse_json_array(file_get_contents('php://input'));
        // $params = array_merge($_REQUEST, is_array($body) ? $body : array());
        $params = array_merge($_REQUEST,$this->_parse_json_array(file_get_contents('php://input')));
        if(!empty($params['data']))
            $params = array_merge($params, $this->_parse_json_array(base64_decode($params['data'])));
            //$params = array_merge($params, json_decode(base64_decode($params['data']), true));
        return $params;
    }

    function response($is_error, $status = 200, $message, $data = array())
    {
        // header("Content-Type: application/json; charset=UTF-8");
        // http_response_code($status);
        $format_response = array('Response' => $is_error, 'Status' => $status, 'Message' => $message);
        if($is_error)
            $format_response = array_merge($format_response, array('data' => $data));
        // http_response_code();
        echo json_encode($format_response);
    }

    function filter($data = array(), $filter = array())
    {
		$result = array();
		foreach($filter as $i => $key)
			if(!empty($data[$key]))
				$result[$key] = $data[$key];
			else
				if(isset($data[$key]) && ($data[$key] == 0 || $data[$key] == ''))
					$result[$key] = $data[$key];
        return $result;
    }
}

$helper = new Helper();
$params = $helper->request();

// parameter yg dipakai ACTION, DATA, API_KEY
if($params && !empty($params['action']) && !empty($params['data']))
{
    if($params['api_key'] === API_KEY)
    {
        switch($params['action']) { //param for searching
            case 'login' :
                $parameter = array('username' => $params['username'], 'password' => $helper->hash_password($params['password']));
                $addons = array('flag_login' => FLAG_LOGIN);
                break;
            case 'checkin' :
                $parameter = array('username' => $params['username'], 'flag_login' => FLAG_LOGIN);
                $addons = array('flag_login' => FLAG_CHECKIN);
                break;
            case 'logout' : //logout
                $parameter = array('username' => $params['username']);
                $addons = array('flag_login' => FLAG_DEFAULT);
                break;
        }
        //select location
        $params = (!empty($params['longitude']) && !empty($params['latitude'])) ? $params : array_merge($params, array('longitude' => 9999, 'latitude' => 9999));
        $location['location'] = $helper->field(array('id','name','address','city','phone'))->isPointerExist($params);
        //select user by $params['action']
        $user = $helper->field(array('username','name','position'))->isUserExist($parameter);
        if($user)
        {
            //filter output for login or logout
            if($params['action'] == 'logout')
            {
                $helper->updateUser(array_merge($user, $addons));
                $helper->response(true, 200, API_SUCCESS_LOGOUT);
            }
            if($params['action'] == 'login')
            {
                $helper->updateUser(array_merge($user, $addons,
                    $helper->logs('lastlogin_datetime','lastlogin_ipaddress', 'lastlogin_useragent')
                ));
                $helper->response(true, 200, API_SUCCESS_LOGIN, array_merge($user, $location));
            }
            if($params['action'] == 'checkin' && $user)
            {
                if($location)
                {
                    $helper->updateUser(array_merge($user, $addons, array('lastcheckin_location' => $location['location']['id'], 'note' => $params['note'], 'flag' => $params['flag']),
                        $helper->logs('lastcheckin_datetime','lastcheckin_ipaddress', 'lastcheckin_useragent')
                    ));
                    $helper->addHistory(array_merge($user, array('location' => $location['location']['id'], 'note' => $params['note'], 'flag' => $params['flag'], 'longitude' => $params['longitude'], 'latitude' => $params['latitude']),
                        array('note' => $params['note'], 'flag' => $params['flag']),
                        $helper->logs('datetime','ipaddress','useragent')
                    ));
                    $helper->response(true, 200, API_SUCCESS_CHECKIN);
                } else
                    $helper->response(false, 404, API_LOCATION_UNKNOWN);
            }
        } else
            $helper->response(false, 404, API_INVALID_CREDENTIALS);
    } else
        $helper->response(false, 404, API_INVALID_ACCESS);
} else
    $helper->response(false, 404, API_MISSING_PARAMETER . ' - ' . json_encode($params));
