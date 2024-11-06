<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Offer extends ActiveRecord
{
    public static function tableName()
    {
        return 'offers';
    }

    public function rules()
    {
        return [
            [['offer_name', 'representative_email'], 'required'],
            [['offer_name', 'representative_email', 'representative_phone'], 'string', 'max' => 255],
            [['representative_email'], 'email'],
            [['representative_email'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'offer_name' => 'Название оффера',
            'representative_email' => 'Email представителя',
            'representative_phone' => 'Телефон представителя',
            'date_added' => 'Дата добавления',
        ];
    }
}
