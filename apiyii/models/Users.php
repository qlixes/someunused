<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "master_user".
 *
 * @property string $username
 * @property string $password
 * @property string $name
 * @property string $position
 * @property string $lastlogin_datetime
 * @property string $lastlogin_ipaddress
 * @property string $lastlogin_useragent
 * @property string $lastcheckin_datetime
 * @property string $lastcheckin_ipaddress
 * @property string $lastcheckin_useragent
 * @property int $lastcheckin_location
 * @property string $note
 * @property int $flag
 * @property int $flag_login
 */
class Users extends \yii\db\ActiveRecord
{
    const FLAG_DEFAULT = 0;
    const FLAG_LOGIN = 1;
    const FLAG_CHECKIN = 2;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'master_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password', 'name', 'position', 'note', 'flag'], 'required'], //scenarios['register']
            [['lastlogin_datetime', 'lastlogin_ipaddress', 'lastlogin_useragent','lastcheckin_datetime', 'lastcheckin_ipaddress','lastcheckin_useragent','lastcheckin_location'], 'safe'],
            [['lastcheckin_location', 'flag', 'flag_login'], 'integer'],
            [['username'], 'string', 'max' => 100],
            [['password', 'name', 'position', 'lastlogin_ipaddress', 'lastlogin_useragent', 'lastcheckin_ipaddress', 'lastcheckin_useragent', 'note'], 'string', 'max' => 255],
            [['username'], 'unique'],
            [['flag_login','flag'], 'default', 'value' => self::FLAG_DEFAULT],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => 'Username',
            'password' => 'Password',
            'name' => 'Name',
            'position' => 'Position',
            'lastlogin_datetime' => 'Lastlogin Datetime',
            'lastlogin_ipaddress' => 'Lastlogin Ipaddress',
            'lastlogin_useragent' => 'Lastlogin Useragent',
            'lastcheckin_datetime' => 'Lastcheckin Datetime',
            'lastcheckin_ipaddress' => 'Lastcheckin Ipaddress',
            'lastcheckin_useragent' => 'Lastcheckin Useragent',
            'lastcheckin_location' => 'Lastcheckin Location',
            'note' => 'Note',
            'flag' => 'Flag',
            'flag_login' => 'Flag Login',
        ];
    }

    public function getLocations()
    {
        return $this->hasOne(Locations::classname, ['id' => 'lastcheckin_location']);
    }

    public function fields()
    {
        return [
            'username',
            'name',
            'position',
            'note',
            'flag',
        ];
    }

    public function extraFields()
    {
        return[
            'location' => function($model) {
                return [
                    'id' => $model->id,
                    'name' => $model->name,
                    'address' => $model->address,
                    'city' => $model->city,
                    'phone' => $model->phone,
                ];
            }
        ];
    }

    public function scenarios()
    {
        return [
            'default' => ['username', 'password', 'name', 'position', 'note', 'flag'],
            'login' => ['username','lastlogin_datetime','lastlogin_ipaddress','lastlogin_useragent','flag_login'],
            'checkin' => ['username', 'lastcheckin_datetime', 'lastcheckin_ipaddress', 'lastcheckin_useragent', 'lastcheckin_location', 'flag_login'],
            'logout' => ['username','flag_login'],
        ];
    }
}
