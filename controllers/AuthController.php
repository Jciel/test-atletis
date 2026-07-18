<?php

namespace app\controllers;

use app\models\forms\LoginForm;
use app\models\forms\RegisterForm;
use app\services\AuthService;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use Yii;

class AuthController extends ApiController
{
    public function __construct($id, $module, private readonly AuthService $authService, $config = [])
    {
        parent::__construct($id, $module, $config);
    }

    public function actionRegister()
    {
        $form = new RegisterForm();

        $this->validateForm($form);

        $result = $this->authService->register($form);

        Yii::$app->response->statusCode = 201;

        return $result;
    }

    public function actionLogin()
    {
        $form = new LoginForm();

        $this->validateForm($form);

        return $this->authService->login($form);
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator']['except'] = [
            'register',
            'login',
        ];

        return $behaviors;
    }

    public function actionMe()
    {
        return Yii::$app->user->identity;
    }
}
