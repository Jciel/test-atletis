<?php

namespace Unit;

use app\models\User;
use app\models\forms\LoginForm;
use app\models\forms\RegisterForm;
use app\services\AuthService;
use Codeception\Test\Unit;
use Yii;
use yii\web\ConflictHttpException;
use yii\web\UnauthorizedHttpException;

class AuthServiceTest extends Unit
{
    private AuthService $authService;

    protected function _before(): void
    {
        $this->authService = Yii::$container->get(AuthService::class);

        User::deleteAll([
            'email' => [
                'john@email.com',
                'mary@email.com',
            ],
        ]);
    }

    public function testRegisterUserSuccessfully(): void
    {
        $form = new RegisterForm();

        $form->username = 'john';
        $form->email = 'john@email.com';
        $form->password = '123456';

        $result = $this->authService->register($form);

        $this->assertTrue($result['success']);

        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('token', $result);

        $this->assertEquals('john', $result['user']['username']);
        $this->assertEquals('john@email.com', $result['user']['email']);

        $this->assertNotEmpty($result['token']);

        $user = User::findByEmail('john@email.com');

        $this->assertNotNull($user);
        $this->assertEquals('john', $user->username);
        $this->assertTrue($user->validatePassword('123456'));
    }

    public function testCannotRegisterWithExistingEmail(): void
    {
        $user = new User();

        $user->username = 'john';
        $user->email = 'john@email.com';
        $user->setPassword('123456');
        $user->generateAuthKey();
        $user->save(false);

        $form = new RegisterForm();

        $form->username = 'mary';
        $form->email = 'john@email.com';
        $form->password = '654321';

        $this->expectException(ConflictHttpException::class);

        $this->authService->register($form);
    }

    public function testLoginSuccessfully(): void
    {
        $user = new User();

        $user->username = 'john';
        $user->email = 'john@email.com';
        $user->setPassword('123456');
        $user->generateAuthKey();
        $user->save(false);

        $form = new LoginForm();

        $form->email = 'john@email.com';
        $form->password = '123456';

        $result = $this->authService->login($form);

        $this->assertArrayHasKey('token', $result);
        $this->assertArrayHasKey('user', $result);

        $this->assertNotEmpty($result['token']);

        $this->assertEquals('john@email.com', $result['user']['email']);
    }

    public function testLoginWithInvalidPassword(): void
    {
        $user = new User();

        $user->username = 'john';
        $user->email = 'john@email.com';
        $user->setPassword('123456');
        $user->generateAuthKey();
        $user->save(false);

        $form = new LoginForm();

        $form->email = 'john@email.com';
        $form->password = 'passwd';

        $this->expectException(UnauthorizedHttpException::class);

        $this->authService->login($form);
    }
}
