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


class UserController extends ActiveController
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

        'only' => ['index', 'view', 'create', 'update', 'delete', 'options','login', 'loginWeb', 'upload', 'search'],
        'rules' => [
            [
                'allow' => true,
                'actions' => ['index', 'view', 'create', 'update', 'delete', 'options', 'upload', 'search'],
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
        if($facebook_user_token){
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

        try {
            $model->facebookLogin($facebook_user_token);
//            $model->facebookLogin("CAAH9dNa9ZAcMBAP4U7W5cCZBhb5UKbCQrhS9e52OWqNxZBgDecBtQUZC33k3pya9WLNgXlSuoFfZBSfpvZCjTLZBAigIfSbHsG7vLZBOTsUbu4wZCzq3RsvVTxi3ZCiOnTL88SxssVdWPiR9fLpfFYZARGulN1zxZAVdHAw6vXgNNIyLi2NdWxLcHmwqvtRO4NrqtvcePmTjV1oCX9iIJGb9FZA48");
        } catch (\Exception $e) {
            return $e->getMessage();
        }

            $model->access_token = $model->generateAccessToken();
            $model->save();

            return  $model->access_token;
        } else {
            return  "There aren't required parameter with name - 'fb_token'";
        }
    }

    public function actionLoginWeb()
    {

        $short_term_token = Yii::$app->getRequest()->post('code');
        if ($short_term_token) {
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

            $model->access_token = $model->generateAccessToken();
            $model->save();

            return  $model->access_token;
        } else {
            return  "There aren't required parameter with name - 'code'";
        }
    }

    public function actionUpload()
    {
        //get user by access token
        $access_token = Yii::$app->getRequest()->get('access-token');
        $user = User::findIdentityByAccessToken($access_token);

        //process the received data
        $model = new UploadForm();

        if (Yii::$app->request->isPost) {
            $model->image = UploadedFile::getInstanceByName('image');  //get uploaded image

            if ($model->image && $model->validate()) {  //if file is present and is valid
                $path_to_file = 'images/img' . time()."_".mt_rand(100,999) . '.' . $model->file->extension; //generate unique name
                $model->image->saveAs($path_to_file); //save image on server
                $user->image = Yii::$app->urlManager->createAbsoluteUrl($path_to_file); //update model
                $user->save();
            }

            User::updateUser($user); //update another fields
        } else {
            return "POST method is only available for this action";
        }
        return $user;
    }

    public function actionProfile()
    {
        //get user by access token
        $access_token = Yii::$app->getRequest()->get('access-token');
        $user = User::findIdentityByAccessToken($access_token);
        return $user;
    }

    public function actionSearch()
    {
        $user_list = [];

        //get user by access token
        $access_token = Yii::$app->getRequest()->get('access-token');
        $user = User::findIdentityByAccessToken($access_token);
        $search_string = Yii::$app->getRequest()->get('search_string');
        $radius = Yii::$app->getRequest()->get('radius');
        $lat = Yii::$app->getRequest()->get('lat');
        $lon = Yii::$app->getRequest()->get('lon');
        if (!$lat) {
            $lat = $user->lat;
        }
        if (!$lon) {
            $lon = $user->lon;
        }
        $my_id  = $user->id;
        
        if (!is_null($search_string)){
            $user_list = User::searchByString($search_string, $my_id);
        } elseif(!is_null($radius) && !is_null($lat) && !is_null($lon)){
            $user_list = User::searchByRadius($lat, $lon, $radius, $my_id);
        } else {
            $user_list = User::find()->andWhere('id!=:my_id',array(':my_id' => $my_id))->all();
        }

        return $user_list;
    }
}