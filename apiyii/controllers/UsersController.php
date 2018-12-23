<?php
namespace app\controllers;

use Yii;
use yii\rest\Controller;
use yii\rest\Serializer;
use app\models\Users;
use app\models\Locations;
use app\models\History;
use yii\helpers\ArrayHelper;

class UsersController extends Controller 
{
    const API_MISSING_CREDENTIALS_PARAMS = 'Missing credentials params';
    const API_INVALID_CREDENTIALS = 'Invalid credentials. Credentials not found';
    const API_MISSING_PROPS_PARAMS = 'Missing property parameter';
    const API_LOCATION_OUT_SERVICE = 'Location out of services';
    const API_INVALID_LOGIN_STATUS = 'Please re-login first';
    const API_SUCCESS_LOGIN = 'Login success';
    const API_SUCCESS_LOGOUT = 'Logout success';
    const API_SUCCESS_CHECKIN = 'Checkin success';
    const API_INVALID_CREDENTIALS_KEY = 'Invalid Api Key';
    const API_INVALID_LOGOUT = 'Invalid logout';
    const API_UNKNOWN_ERROR = 'Unknown Errors';

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function behaviors()
    {
        return [
            // 'authenticator' => [
            //     'class' => \yii\filters\auth\HttpBasicAuth::className(),
            // ],
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
            ],
        ];
    }

    protected function verbs()
    {
        return [
            'login' => ['POST'],
            'checkin' => ['POST'],
            'logout' => ['POST'],
            '*' => ['GET'],
        ];
    }

    public function actionIndex()
    {
        echo 'index';
    }

    public function actionLogin()
    {
        $component = \Yii::$app->mycomponents;

        $params = $component->load_params(\Yii::$app->params['labelBase64']);
        
        if(!empty($params['username']) && !empty($params['password']) && !empty($params['api_key']))
        {
            if($component->validateApiKey($params['api_key']))
            {
                $models = new Users(['scenario' => 'login']);
                $location = new Locations();
                $params = ArrayHelper::merge($params, ['password' => \Yii::$app->mycomponents->hash_password($params['password'])]);

                $param_login = $component->filter_used($params, ['username','password']);

                $data_login = $models->find()->where($param_login)->one();
                if($data_login)
                {
                    //check location
                    if(!empty($params['longitude']) && !empty($params['latitude']))
                        $check = $location->findByPointer($params['longitude'], $params['latitude']);
                    else
                        $check = array();

                    $params = ArrayHelper::merge($params, $component->format_log('lastlogin_datetime','lastlogin_ipaddress','lastlogin_useragent'), ['flag_login' => Users::FLAG_LOGIN]);

                    $update = $models->findOne($data_login->username);
                    $update->lastlogin_datetime = $params['lastlogin_datetime'];
                    $update->lastlogin_ipaddress = $params['lastlogin_ipaddress'];
                    $update->lastlogin_useragent = $params['lastlogin_useragent'];
                    $update->flag_login = $params['flag_login'];
                    $update->save(false);
                    
                    // next update will use extraField on models
                    $final = ArrayHelper::merge($data_login->toArray(), ['location' => $check]);
                    $component->response(true, 200, self::API_SUCCESS_LOGIN, $final);

                } else
                    $component->response(false, 404, self::API_INVALID_CREDENTIALS);
            } else
                $component->response(false, 404, self::API_INVALID_CREDENTIALS_KEY);
        } else
            $component->response(false, 404, self::API_MISSING_CREDENTIALS_PARAMS);
    }

    public function actionLogout()
    {
        $component = \Yii::$app->mycomponents;

        $params = $component->load_params(\Yii::$app->params['labelBase64']);
        
        if(!empty($params['username']) && !empty($params['api_key']))
        {
            if($component->validateApiKey($params['api_key']))
            {
                $models = new Users(['scenario' => 'logout']);

                $param_login = $component->filter_used($params, ['username']);

                $data_login = $models->find()->where($param_login)->one();
                if($data_login)
                {
                    $update = $models->findOne($data_login->username);
                    $update->flag_login = Users::FLAG_DEFAULT;
                    $update->save(false);

                    $component->response(true, 200, self::API_SUCCESS_LOGOUT);
                } else
                    $component->response(false, 404, self::API_INVALID_CREDENTIALS);
            } else
                $component->response(false, 404, self::API_INVALID_CREDENTIALS_KEY);
        } else
            $component->response(false, 404, self::API_MISSING_CREDENTIALS_PARAMS);
    }

    public function actionCheckin()
    {
        $component = \Yii::$app->mycomponents;

        $params = $component->load_params(\Yii::$app->params['labelBase64']);
        
        if(!empty($params['username']) && !empty($params['api_key']))
        {
            if($component->validateApiKey($params['api_key']))
            {
                $models = new Users(['scenario' => 'checkin']);
                $location = new Locations();
                $params = ArrayHelper::merge($params, ['flag_login' => Users::FLAG_LOGIN]);
                $param_login = $component->filter_used($params, ['username','flag_login']);

                $data_login = $models->find()->where($param_login)->one();
                if($data_login)
                {
                    //check location
                    if(!empty($params['longitude']) && !empty($params['latitude']))
                    {
                        $check = $location->findByPointer($params['longitude'], $params['latitude']);

                        if($check)
                        {
                            $params = ArrayHelper::merge($params, $component->format_log('lastcheckin_datetime','lastcheckin_ipaddress','lastcheckin_useragent'),['flag_login' => Users::FLAG_CHECKIN, 'lastcheckin_location' => $check->id]);
                            $update = $models->findOne($data_login->username);
                            $update->lastcheckin_datetime = $params['lastcheckin_datetime'];
                            $update->lastcheckin_ipaddress = $params['lastcheckin_ipaddress'];
                            $update->lastcheckin_useragent = $params['lastcheckin_useragent'];
                            $update->lastcheckin_location = $params['lastcheckin_location'];
                            $update->note = $params['note'];
                            $update->flag = $params['flag'];
                            $update->flag_login = $params['flag_login'];
                            $update->save(false);

                            $component->response(true, 200, self::API_SUCCESS_CHECKIN);

                            $history = new History();
                            $history->username = $params['username'];
                            $history->datetime = $params['lastcheckin_datetime'];
                            $history->location = $params['lastcheckin_location'];
                            $history->ipaddress = $params['lastcheckin_ipaddress'];
                            $history->useragent = $params['lastcheckin_useragent'];
                            $history->note = $params['note'];
                            $history->flag = $params['flag'];
                            $history->save(false);
                            
                        } else
                            $component->response(false, 404, self::API_LOCATION_OUT_SERVICE);
                    }
                    else
                        $component->response(false, 404, self::API_MISSING_PROPS_PARAMS);
                } else
                    $component->response(false, 404, self::API_INVALID_LOGIN_STATUS);
            } else
                $component->response(false, 404, self::API_INVALID_CREDENTIALS_KEY);
        } else
            $component->response(false, 404, self::API_MISSING_CREDENTIALS_PARAMS);
    }
}