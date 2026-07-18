<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;

class SiteController extends Controller
{
    public function actionIndex(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['name' => 'Atletis API', 'version' => '1.0.0', 'status' => 'online'];
    }
}
