<?php

namespace api\controllers;

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
}