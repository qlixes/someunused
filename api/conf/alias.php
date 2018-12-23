<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// allowed for change value name
// forbidden using whitespace or will be fucked up
// show on output json_encode()
$output['username'] = 'akun';
$output['password'] = 'sandi';
$output['name'] = 'nama';
$output['position'] = 'jabatan';
$output['datetime'] = 'tgl';
$output['location'] = 'lokasi';
$output['ipaddress'] = 'alamat_ip';
$output['useragent'] = 'peramban';
$output['note'] = 'catatan';
$output['flag'] = 'tanda';
$output['lastlogin_datetime'] = 'login_tgl';
$output['lastlogin_ipaddress'] = 'login_ip';
$output['lastlogin_useragent'] = 'login_peramban';
$output['lastcheckin_datetime'] = 'checkin_tgl';
$output['lastcheckin_ipaddress'] = 'checkin_ip';
$output['lastcheckin_useragent'] = 'checkin_peramban';
$output['lastcheckin_location'] = 'checkin_lokasi';
$output['flag_login'] = 'status_login';
$output['address'] = 'alamat';
$output['phone'] = 'telp';
$output['latitude_min'] = 'latitude_start';
$output['latitude_max'] = 'latitude_end';
$output['longitude_min'] = 'longitude_start';
$output['longitude_max'] = 'longitude_end';
$output['id'] = 'no';
$output['city'] = 'kota';

// allowd for change index name
// using for confirm using param name
$input['username'] = 'username';
$input['password'] = 'password';
$input['name'] = 'name';
$input['position'] = 'position';
$input['timestamp'] = 'datetime';
$input['location'] = 'location';
$input['ipaddress'] = 'ipaddress';
$input['useragent'] = 'useragent';
$input['note'] = 'note';
$input['flag'] = 'flag';
$input['login_time'] = 'lastlogin_datetime';
$input['login_ip'] = 'lastlogin_ipaddress';
$input['login_agent'] = 'lastlogin_useragent';
$input['checkin_time'] = 'lastcheckin_datetime';
$input['checkin_ip'] = 'lastcheckin_ipaddress';
$input['checkin_agent'] = 'lastcheckin_useragent';
$input['checkin_location'] = 'lastcheckin_location';
$input['flag_login'] = 'flag_login';
$input['address'] = 'address';
$input['phone'] = 'phone';
$input['latitude'] = 'latitude';
$input['longitude'] = 'longitude';
$input['id'] = 'id';
$input['city'] = 'city';
$input['api_key'] = 'api_key';
