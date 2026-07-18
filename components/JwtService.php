<?php

declare(strict_types=1);

namespace app\components;

use app\models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Yii;
use yii\base\Component;
use yii\base\Exception;

class JwtService extends Component
{
    private string $algorithm = 'HS256';

    public function generate(User $user): string
    {
        $now = time();

        $data = array_merge([
            'iss' => Yii::$app->params['jwt']['issuer'],
            'iat' => $now,
            'exp' => $now + Yii::$app->params['jwt']['expire']
        ], ['sub' => $user->id]);

        return JWT::encode($data, Yii::$app->params['jwt']['secret'], $this->algorithm);
    }

    public function validate(string $token): object
    {
        try {
            return JWT::decode($token, new Key(Yii::$app->params['jwt']['secret'], $this->algorithm));
        } catch (\Exception $e) {
            throw new Exception('Token inválido.');
        }
    }
}
