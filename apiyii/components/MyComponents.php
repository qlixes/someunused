<?php
namespace app\components;

use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;

class MyComponents extends Component
{
    // changeable by requested
    public function hash_password($password)
    {
        return sha1($password);
    }

    public function validateApiKey($apiKey)
    {
        return ($apiKey === \Yii::$app->params['apiKey']);
    }

    public function filter_used($collection = array(), $filter = array())
    {
        $result = array();
        foreach($filter as $key)
            if((null !== $collection[$key]) && (!empty($collection[$key]) || $collection[$key] == '' || $collection[$key] == 0))
                $result[$key] = $collection[$key];
            
        return $result;
    }

    // format datetime, ipaddress, useragent
    public function format_log($label_datetime, $label_ipaddress, $label_useragent)
    {
        $request = new \yii\web\Request();
        return [
            $label_datetime => new \yii\db\Expression('NOW()'),
            $label_ipaddress => $request->getUserIP(),
            $label_useragent => $request->getUserAgent()
        ];
    }

    // next update, while false, then will ignore labelBase64
    public function load_params($label_key = null)
    {
        $request = \Yii::$app->request->post();
        if($label_key && \Yii::$app->params['useBase64'])
            $request = $request[$label_key];
        
        return (\Yii::$app->params['useBase64']) ? json_decode(base64_decode($request), true) : $request;
    }

    public function response($condition, $status_code = 200, $message, $data = [])
    {
        $response = \Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->statusCode = $status_code;
        $format_response = ['message' => $message, 'status' => $status_code, 'name' => ''];
        if($condition)
            $format_response = ArrayHelper::merge($format_response, ['data' => $data]);

        $response->data = $format_response;
    }
}