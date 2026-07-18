<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'container' => [
        'singletons' => [
            app\components\JwtService::class => app\components\JwtService::class,
            app\services\AuthService::class => function ($container) {
                return new app\services\AuthService($container->get(app\components\JwtService::class));
            },
            \yii\mail\MailerInterface::class => [
                'class' => \yii\symfonymailer\Mailer::class,
                // send all mails to a file by default.
                'useFileTransport' => true,
                'viewPath' => '@app/mail',
            ],
        ],
    ],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'jwt' => [
            'class' => \app\components\JwtService::class,
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'cJFtPm7u2ZSRunXBju3VvvNpS6N_aKKY',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'user' => [
            'identityClass' => \app\models\User::class,
            'enableAutoLogin' => false,
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => \yii\mail\MailerInterface::class,
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                [
                    'class' => yii\rest\UrlRule::class,
                    'controller' => ['auth'],
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST register' => 'register',
                        'POST login' => 'login',
                    ],
                ],
                [
                    'class' => yii\rest\UrlRule::class,
                    'controller' => ['expense-category'],
                    'pluralize' => true,
                    'extraPatterns' => [
                        'GET'  => 'index',
                        'GET {id}' => 'view',
                        'POST' => 'create',
                        'PUT {id}' => 'update',
                        'DELETE {id}' => 'delete',
                    ],
                ],
                [
                    'class' => yii\rest\UrlRule::class,
                    'controller' => ['expense'],
                    'pluralize' => true,
                    'extraPatterns' => [
                        'GET'  => 'index',
                        'GET {id}' => 'view',
                        'POST' => 'create',
                        'PUT {id}' => 'update',
                        'DELETE {id}' => 'delete',
                    ],
                ],
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => \yii\debug\Module::class,
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => \yii\gii\Module::class,
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
