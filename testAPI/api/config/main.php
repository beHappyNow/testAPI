<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
//            'enableStrictParsing' => true,//comment this line to set app in web mode
            'showScriptName' => false,
            'rules' => [
                'POST login' => 'user/login',
                'POST login-web' => 'user/login-web',
                'POST profile' => 'user/upload',
                'GET profile' => 'user/profile',
                'GET users/search' => 'user/search',
                'POST location' => 'location/update-location',
                ['class' => 'yii\rest\UrlRule', 'controller' => 'user'],

            ],
        ],
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ]

    ],
    'params' => $params,
];
