<?php

namespace api\controllers;

use Yii;
use app\models\User;
use app\models\UploadForm;
use yii\web\UploadedFile;
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

        'only' => ['index', 'view', 'create', 'update', 'delete', 'options','login', 'loginWeb', 'upload'],
        'rules' => [
            [
                'allow' => true,
                'actions' => ['index', 'view', 'create', 'update', 'delete', 'options', 'upload'],
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
        $facebook_user_token = Yii::$app->getRequest()->post('fb_token');
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

        $model->facebookLogin($facebook_user_token);


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
        $short_term_token = Yii::$app->getRequest()->post('code');
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

        $model->facebookLoginWeb($short_term_token);


        var_dump($model->attributes);
        var_dump($model);
        $model->access_token = $model->generateAccessToken();
        var_dump($model->save());
        var_dump($model);

        return  $model->access_token;
    }

    public function actionUpload()
    {
        $access_token = Yii::$app->getRequest()->get('access-token');
        $user = User::findIdentityByAccessToken($access_token);

//        var_dump($user);
        var_dump($_REQUEST);
        var_dump($_FILES);

//        die;

//        User::updateUser($user);

        $model = new UploadForm();

        if (Yii::$app->request->isPost) {
            $model->image = UploadedFile::getInstance($model, 'file');
            var_dump($model);die();
            if ($model->image && $model->validate()) {
                $path_to_file = 'images/img' . time()."_".mt_rand(100,999) . '.' . $model->file->extension;
                $model->image->saveAs($path_to_file);
                $user->image = Yii::$app->urlManager->createAbsoluteUrl($path_to_file);
                $user->save();
            }
        }

        return $user;
    }
}