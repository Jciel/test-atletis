<?php

namespace app\controllers;

use app\components\JwtAuth;
use Yii;
use yii\base\Model;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;

class ApiController extends Controller
{
    protected function validateForm(Model $form): void
    {
        $form->load(Yii::$app->request->bodyParams, '');

        if (!$form->validate()) {
            throw new BadRequestHttpException(
                json_encode([
                    'message' => 'Validation failed.',
                    'errors' => $form->errors,
                ])
            );
        }
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['application/json'] = \yii\web\Response::FORMAT_JSON;

        $behaviors['authenticator'] = [
            'class' => JwtAuth::class,
        ];

        return $behaviors;
    }
}
