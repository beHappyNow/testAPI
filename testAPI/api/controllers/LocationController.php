<?php

namespace api\controllers;

use Yii;
use app\models\User;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\web\ServerErrorHttpException;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;


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
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        // unset actions "index", "view", "delete" and "create"
        unset($actions['view'],$actions['delete'], $actions['create'], $actions['update']);

        return $actions;
    }

    public function actionUpdate($access_token)
    {
        $model = User::findIdentityByAccessToken($access_token);

        User::updateUser($model);

        return $model;
    }
}