<?php

namespace api\controllers;

use Yii;
use app\models\User;
use yii\helpers\Url;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\AccessControl;


class UserrController extends ActiveController
{
    public $modelClass = 'app\models\User';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
            'except' => ['login', 'login-web']
        ];

        $behaviors['access'] = [
        'class' => AccessControl::className(),

        'only' => ['index', 'view', 'create', 'update', 'delete', 'options','login', 'loginWeb'],
        'rules' => [
            [
                'allow' => true,
                'actions' => ['index', 'view', 'create', 'update', 'delete', 'options'],
                'roles' => ['@'],
            ],
            [
                'allow' => true,
                'actions' => ['login', 'loginWeb'],
                'roles' => ['?'],
            ],
        ],
    ];
        return $behaviors;
    }

    public function actionLogin()
    {
        $str =  Yii::$app->urlManager->createAbsoluteUrl('login');

/*        $link = "http://test-api.live.gbksoft.net/api/web/images/18.jpg";
        $file = file_get_contents($link);
        file_put_contents("images/img".time()."_".mt_rand(100,999).".jpg", $file);*/
        $values = [
            'username' => 'Your username',
            'email' => 'your@email.com',
            'created_at' => time(),
            'updated_at' => time(),
            'first_name' => '',
            'last_name' => '',
            'image' => '',
            'lat' => '',
            'lon' => '',
            'city' => '',
            'country' => '',
            'gender' => '',
            'access_token' => '',
            'fb_token' => '',
            'api_token' => '',
        ];

        $model = new User();
        $model->attributes = $values;

        $response = $model->facebookLogin();
        switch ($response['status']) {
            case "redirect":
            case "redirect_error":
            case "success":
            return $this->redirect($response['url'], 302);
            break;
            case "error":
                return $response['message'];
                break;
        }

        var_dump($model->attributes);
        var_dump($model);
        $model->access_token = $model->generateAccessToken();
        var_dump($model->save());
        var_dump($model);

        return  $model->access_token;
        die();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
    }

    public function actionLoginWeb()
    {
        $model = new User();

    }
}