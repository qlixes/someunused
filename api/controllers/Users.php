<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require COREPATH . 'Controller.php';

class Users extends Controller implements IController
{
    var $master = array();

    function __construct()
    {
        parent::__construct();
        $this->master = $this->filter_default($this->getRequestParams2(), array(
            'lastlogin_ipaddress' => $this->get_ip(),
            'lastlogin_useragent' => $this->get_agent(),
            'ipaddress' => $this->get_ip(),
            'useragent' => $this->get_agent(),
            'lastcheckin_ipaddress' => $this->get_ip(),
            'lastcheckin_useragent' => $this->get_agent(),
            'note' => '',
            'flag' => FLAG_DEFAULT,
            'flag_login' => FLAG_IS_LOGIN,
        ));
    }

    function index()
    {
        $this->getResponse(false, 'label_api_not_found');
    }

    function _check_login($need_login = true)
    {
        $select = array('username');
        if($need_login)
            $select[] = 'password';
        else
            $select[] = 'flag_login';

        list($flag_user, $data_user) = $this->filter_used($this->master, $select);
        list($flag_api, $data_api) = $this->filter_used($this->master, array('api_key','username'));

        return array(($flag_user || $flag_api), $data_user, $data_api);
    }

    function login()
    {
        $this->master = array_merge($this->master, array('lastlogin_datetime' => $this->formatdatemon('now', true)));

        list($flag_user, $data_user, $data_api) = $this->_check_login();

        if($flag_user) // params login if completed
        {
            if(!empty($data_user['password']))
                $data_user = array_merge($data_user, array('password' => $this->hash_password($data_user['password'])));

            //check login in db
            //keluarkan params result utk user add di alias
            list($flag_login, $data_login) = $this->users->alias(array('username','position'))->selectUser($data_user);

            if($data_login || ($data_api === API_KEY))
            {
                //check lastlogin params
                list($flag_update, $data_update) = $this->filter_used($this->master, array('lastlogin_datetime', 'lastlogin_ipaddress','lastlogin_useragent','username','flag_login'));

                // check params must exists
                list($flag_location, $data_location) = $this->filter_used($this->master, array('latitude','longitude'));

                $check_location = ($data_location && $data_location['longitude'] !== '' && $data_location['latitude'] !== '');
                if($data_update && $check_location)
                {
                    // update lastlogin now
                    $update_login = $this->users->upLastLogin($data_update);

                    list($flag_lokasi, $data_lokasi) = $this->users->alias(array('id','name','address','city'))->selectArea($data_location);

                    if($data_lokasi)
                    {
                        // push array lokasi ke result
                        $final = array_merge($data_login, array($this->output('location') => $data_lokasi));

                        $this->getResponse($data_lokasi, 'label_api_success_login', $final);
                    } else
                        $this->getResponse($data_lokasi, 'label_api_missing_location');
                } else
                    $this->getResponse(($data_update && $check_location), 'label_api_missing_parameter');
            } else
                $this->getResponse($data_login, 'label_api_failed_login');
        } else
            $this->getResponse($flag_user, 'label_api_missing_login');
    }

    function logout()
    {
        $this->master = array_merge($this->master, array('flag_login' => FLAG_DEFAULT));

        list($flag_logout, $data_logout) = $this->filter_used($this->master, array('username','flag_login'));

        $check = $this->users->upLastLogout($data_logout);

        $this->getResponse($check, 'label_api_success_logout');
    }

    function checkin()
    {
        $this->master = array_merge($this->master, array('flag_login' => FLAG_IS_LOGIN, 'lastcheckin_datetime' => $this->formatdatemon('now', true), 'datetime' => $this->formatdatemon('now', true)));

        list($flag_user, $data_user, $data_api) = $this->_check_login(false);

        // $check_auth = ($data_user || ($data_user && $data_api && $data_api['api_key'] === API_KEY));
        if($flag_user)
        {
            list($flag_login, $data_login) = $this->users->selectUser($data_user);

            if($data_login) //check login
            {
                //check koordinat
                list($flag_location, $data_location) = $this->filter_used($this->master, array('longitude','latitude'));
                $check_location = ($data_location && $data_location['longitude'] !== '' && $data_location['latitude'] !== '');
                if($check_location)
                {
                    list($flag_lokasi, $data_lokasi) = $this->users->selectArea($data_location);

                    if($data_lokasi)
                    {
                        list($flag_update, $data_update) = $this->filter_used($this->master, array('username','lastcheckin_ipaddress','lastcheckin_datetime','lastcheckin_useragent'));

                        //force parse for update
                        $data_update = array_merge($data_update, array('flag_login' => FLAG_IS_CHECKIN, 'lastcheckin_location' => $data_lokasi['id']));

                        list($flag_history, $data_history) = $this->filter_used($this->master, array('username','datetime','ipaddress','useragent','note','flag'));

                        $data_history['location'] = $data_lokasi['id'];
                        list($flag_history, $id_history) = $this->users->addHistory($data_history);
                        $check_update = $this->users->upLastCheckIn($data_update);

                        $this->getResponse(true, 'label_api_success_checkin');
                    } else
                        $this->getResponse($data_lokasi, 'label_api_missing_location');
                } else
                    $this->getResponse($check_location, 'label_api_missing_parameter');
            } else
                $this->getResponse($data_login, 'label_api_need_login');
        } else
            $this->getResponse($flag_user, 'label_api_missing_parameter');
    }
}
