<?php

declare(strict_types=1);

namespace app\components;

use Yii;
use yii\filters\auth\AuthMethod;
use yii\web\UnauthorizedHttpException;

class JwtAuth extends AuthMethod
{
    public function authenticate($user, $request, $response)
    {
        $header = $request->getHeaders()->get('Authorization');

        if (!$header) { return null; }

        if (!preg_match('/^Bearer\s+(.*?)$/', $header, $matches)) {
            throw new UnauthorizedHttpException('Token inválido.');
        }

        $token = $matches[1];

        try {
            return $user->loginByAccessToken($token, get_class($this));
        } catch (\Exception $e) {
            throw new UnauthorizedHttpException('Token inválido.');
        }
    }
}
