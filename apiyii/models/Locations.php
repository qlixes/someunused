<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "master_location".
 *
 * @property int $id
 * @property string $name
 * @property string $address
 * @property string $city
 * @property string $phone
 * @property string $latitude_min
 * @property string $latitude_max
 * @property string $longitude_min
 * @property string $longitude_max
 */
class Locations extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'master_location';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'address', 'phone', 'city', 'latitude_min', 'latitude_max', 'longitude_min', 'longitude_max'], 'required'],
            [['latitude_min', 'latitude_max', 'longitude_min', 'longitude_max'], 'number'],
            [['name', 'address', 'city', 'phone'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'address' => 'Address',
            'city' => 'City',
            'phone' => 'Phone',
            'latitude_min' => 'Latitude Min',
            'latitude_max' => 'Latitude Max',
            'longitude_min' => 'Longitude Min',
            'longitude_max' => 'Longitude Max',
        ];
    }

    public function fields()
    {
        return [
            'id',
            'name',
            'address',
            'city',
            'phone'
        ];
    }

    public function findByPointer($longitude, $latitude)
    {
        return self::find()->where(['>=', 'latitude_min', $latitude])->andWhere(['<=', 'latitude_max' ,$latitude])
                            ->andWhere(['<=', 'longitude_min', $longitude])->andWhere(['>=', 'longitude_max', $longitude])
                            ->one();
    }
}
