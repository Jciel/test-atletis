<?php

namespace app\commands;

use Yii;
use yii\console\Controller;

class TestController extends Controller
{
    public function actionJwt()
    {
        $token = Yii::$app->jwt->generate(['sub' => 1]);

        echo "TOKEN:\n";
        echo $token . PHP_EOL . PHP_EOL;

        $payload = Yii::$app->jwt->validate($token);

        print_r($payload);
    }
}
