<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\web\ServerErrorHttpException;
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\Entities\AccessToken;
use Facebook\HttpClients\FacebookCurlHttpClient;
use Facebook\HttpClients\FacebookHttpable;

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
            [['username', 'email', 'created_at', 'updated_at'], 'required'],
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
        unset($fields['auth_key'], $fields['password_hash'],$fields['username'],$fields['status'], $fields['password_reset_token'], $fields['access_token'], $fields['fb_token'], $fields['api_token']);

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

    public static function updateUser($model)
    {
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        $model->updated_at = time();
        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }
    }

    public function facebookLoginWeb($short_term_token)
    {
        try {
            FacebookSession::enableAppSecretProof(false);
            $session = new FacebookSession($short_term_token);
            $accessToken = new AccessToken($short_term_token);
            // get long term token
            $facebook_user_token = $accessToken->extend(Yii::$app->params['facebookApp']['app_id'], Yii::$app->params['facebookApp']['app_secret']);
            if (!$this->fb_token) {
                $this->fb_token = $facebook_user_token;

                // graph api request for user data
                $request = new FacebookRequest( $session, 'GET', "/me", array(
                    'fields' => 'picture,email,name',
                ));

                $me = $request->execute()->getGraphObject()->asArray();

                if (isset($me['name'])) {
                    $this->username = $me['name'];
                    $name = explode(" ", $me['name']);

                    if(count($name) > 1){
                        $this->first_name = $name[0];
                        $this->last_name = $name[1];
                    }
                }

                if (isset($me['email'])) {
                    $this->email = $me['email'];
                }

                if (isset($me['picture']->data->url)) {
//                    $link = "http://test-api.live.gbksoft.net/api/web/images/18.jpg";
                    $link = $me['picture']->data->url;
                    $file = file_get_contents($link);
                    $path_to_file = "images/img".time()."_".mt_rand(100,999).".jpg";
                    file_put_contents($path_to_file, $file);

                    $this->image = Yii::$app->urlManager->createAbsoluteUrl($path_to_file);
                }
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function facebookLogin($facebook_user_token)
    {
        $this->fb_token = $facebook_user_token;

        FacebookSession::enableAppSecretProof(false);
        $longLivedAccessToken = new AccessToken($facebook_user_token);
        $session = new FacebookSession($longLivedAccessToken);
        $request = new FacebookRequest(
            $session,
            'GET',
            "/me",
            array('fields' => 'picture,email,name',)
        );
        try {
            $me = $request->execute()->getGraphObject()->asArray();

            if (isset($me['name'])) {
                $this->username = $me['name'];
                $name = explode(" ", $me['name']);

                if(count($name) > 1){
                    $this->first_name = $name[0];
                    $this->last_name = $name[1];
                }
            }

            if (isset($me['email'])) {
                $this->email = $me['email'];
            }

            if (isset($me['picture']->data->url)) {
//                    $link = "http://test-api.live.gbksoft.net/api/web/images/18.jpg";
                $link = $me['picture']->data->url;
                $file = file_get_contents($link);
                $path_to_file = "images/img".time()."_".mt_rand(100,999).".jpg";
                file_put_contents($path_to_file, $file);

                $this->image = Yii::$app->urlManager->createAbsoluteUrl($path_to_file);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function facebookFullLogin()
    {
        $response = array(
            'status' => false,
        );

        if (!empty($this->fb_token)) return $response;


        $helper = new FacebookRedirectLoginHelper(
            Yii::$app->urlManager->createAbsoluteUrl('login'),
            Yii::$app->params['facebookApp']['app_id'],
            Yii::$app->params['facebookApp']['app_secret']
        );
        FacebookSession::enableAppSecretProof(false);
        $session = $helper->getSessionFromRedirect();
        $userdata = isset($_SESSION['check_login']) ? $_SESSION['check_login'] : false;
        if (isset($session) && $userdata) {
            // get long term token
            $accessToken = $session->getAccessToken();
            $facebook_user_token = $accessToken->extend(Yii::$app->params['facebookApp']['app_id'], Yii::$app->params['facebookApp']['app_secret']);
            // graph api request for user data
            if (!$this->fb_token) {
                $this->fb_token = $facebook_user_token;

                $request = new FacebookRequest( $session, 'GET', "/me", array(
                    'fields' => 'picture,email,name',
                ));

                $me = $request->execute()->getGraphObject()->asArray();

                if (isset($me['name'])) {
                    $this->username = $me['name'];
                }

                if (isset($me['email'])) {
                    $this->email = $me['email'];
                }

                if (isset($me['picture']->data->url)) {
//                    $link = "http://test-api.live.gbksoft.net/api/web/images/18.jpg";
                    $link = $me['picture']->data->url;
                    $file = file_get_contents($link);
                    $path_to_file = "images/img".time()."_".mt_rand(100,999).".jpg";
                    file_put_contents($path_to_file, $file);

                    $this->image = Yii::$app->urlManager->createAbsoluteUrl($path_to_file);
                }
            }
        } else {
            $permissions = array(
                'email',
                'public_profile',
            );
            $_SESSION['check_login'] = true;
            $loginUrl = $helper->getLoginUrl($permissions);
            $response = array(
                'status' => 'redirect',
                'url'    => $loginUrl,
            );
        }
        return $response;
    }




    public function generateAccessToken()
    {
        $str = $this->created_at."salt";
        return md5($str);
    }
}
