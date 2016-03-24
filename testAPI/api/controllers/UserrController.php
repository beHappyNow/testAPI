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
use \ImageHandler\CImageHandler;


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

        $ih = new CImageHandler();
//                    $ih->load($me['picture']->data->url);
        $ih->load("http://test-api.live.gbksoft.net/api/web/images/18.jpg");
        $img_url = Yii::$app->urlManager->createAbsoluteUrl('web/images').mt_rand(0,50)."jpg";
        $ih->save($img_url);

        return  $str;

        die();
        $model = new User();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
    }

    public function actionLoginWeb()
    {
        $model = new User();

    }
}