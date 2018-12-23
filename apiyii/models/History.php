<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "data_checkin".
 *
 * @property int $id
 * @property string $username
 * @property string $datetime
 * @property int $location
 * @property string $ipaddress
 * @property string $useragent
 * @property string $note
 * @property int $flag
 */
class History extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'data_checkin';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'datetime', 'location', 'ipaddress', 'useragent', 'note', 'flag'], 'required'],
            [['datetime'], 'safe'],
            [['location', 'flag'], 'integer'],
            [['username', 'ipaddress', 'useragent', 'note'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'datetime' => 'Datetime',
            'location' => 'Location',
            'ipaddress' => 'Ipaddress',
            'useragent' => 'Useragent',
            'note' => 'Note',
            'flag' => 'Flag',
        ];
    }
}
