<?php

namespace api\controllers;

use Yii;
use app\models\User;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * test controller used to explore the possibilities of the framework
 * the only useful action actionUpdateLocation() can be implemented in UserController
 */
class LocationController extends ActiveController
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
        ];
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'update' => ['post','put'],
            ],
        ];
        $behaviors['access'] = [
            'class' => AccessControl::className(),

            'only' => ['index', 'view', 'create', 'update', 'delete', 'options'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['index', 'view', 'create', 'update', 'delete', 'options'],
                    'roles' => ['@'],
                ],
            ],
        ];
        $behaviors['timestamp'] = [
            'class' => TimestampBehavior::className(),
            'attributes' => [
                ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
            ],
        ];
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        // unset actions "index", "view", "delete" and "create"
        unset($actions['view'],$actions['delete'], $actions['create'], $actions['update']);

        return $actions;
    }

    public function actionUpdateLocation()
    {
        $access_token = Yii::$app->getRequest()->get('access-token');
        $model = User::findIdentityByAccessToken($access_token);

        User::updateUser($model);

        return $model;
    }
}