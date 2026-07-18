<?php

declare(strict_types=1);

namespace app\tests\Unit;
use app\components\JwtService;

use Codeception\Test\Unit;
use Yii;
use yii\base\Exception;
use app\models\User;

class JwtServiceTest extends Unit
{

    private JwtService $jwtService;

    protected function _before(): void
    {
        $this->jwtService = Yii::$app->jwt;
    }

    public function testGenerateToken(): void
    {
        $user = new User();
        $user->id = 1;
        $user->email = 'jhon@email.com';
        $user->username = 'jhon';

        $token = $this->jwtService->generate($user);

        $this->assertIsString($token);
        $this->assertNotEmpty($token);
    }

    public function testValidateToken(): void
    {
        $user = new User();
        $user->id = 1;
        $user->email = 'jhon@email.com';
        $user->username = 'jhon';

        $token = $this->jwtService->generate($user);

        $decoded = $this->jwtService->validate($token);

        $this->assertEquals($user->id, $decoded->sub);

        $this->assertObjectHasProperty('iss', $decoded);
        $this->assertObjectHasProperty('iat', $decoded);
        $this->assertObjectHasProperty('exp', $decoded);
    }

    public function testInvalidTokenThrowsException(): void
    {
        $this->expectException(Exception::class);

        $this->jwtService->validate('invalid-token');
    }
}
