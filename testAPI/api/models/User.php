<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $first_name
 * @property string $last_name
 * @property string $image
 * @property string $lat
 * @property string $lon
 * @property string $city
 * @property string $country
 * @property string $gender
 */
class User extends ActiveRecord implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'auth_key', 'password_hash', 'email', 'created_at', 'updated_at'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['username', 'password_hash', 'password_reset_token', 'email', 'first_name', 'last_name', 'image', 'lat', 'lon', 'city', 'country', 'gender'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
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
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'image' => 'Image',
            'lat' => 'Lat',
            'lon' => 'Lon',
            'city' => 'City',
            'country' => 'Country',
            'gender' => 'Gender',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();

        //remove unsafe fields from response
        unset($fields['auth_key'], $fields['password_hash'],$fields['username'],$fields['status'], $fields['password_reset_token']);

        return $fields;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    public function getId()
    {
         return $this->id;
    }

    public static function findIdentity($id)
    {
    }

    public function getAuthKey()
    {
    }

    public function validateAuthKey($authKey)
    {
    }
}
