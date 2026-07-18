<?php

namespace app\services;

use app\components\JwtService;
use app\models\User;
use app\models\forms\LoginForm;
use app\models\forms\RegisterForm;
use yii\web\ConflictHttpException;
use yii\web\UnauthorizedHttpException;

class AuthService extends BaseService
{
    public function __construct(private readonly JwtService $jwtService)
    {}

    public function register(RegisterForm $registerForm): array
    {
        if (User::findByEmail($registerForm->email)) {
            throw new ConflictHttpException("E-mail already exists.");
        }

        $user = new User();

        $user->email = $registerForm->email;
        $user->username = $registerForm->username;

        $user->setPassword($registerForm->password);

        $user->generateAuthKey();

        $this->saveOrFail($user);

        $token = $this->jwtService->generate($user);

        return [
            'success' => true,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
            ],
            'token' => $token,
        ];
    }

    public function login(LoginForm $loginForm): array
    {
        $user = User::findByEmail($loginForm->email);

        if (!$user || !$user->validatePassword($loginForm->password)) {
            throw new UnauthorizedHttpException('E-mail ou senha inválidos.');
        }

        $token = $this->jwtService->generate($user);

        return [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
            ],
        ];
    }
}
