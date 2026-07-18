<?php

namespace Unit;

use Codeception\Test\Unit;
use app\models\forms\RegisterForm;

class RegisterFormTest extends Unit
{
    public function testRegisterFormIsValidWithCorrectData(): void
    {
        $form = new RegisterForm();
        $form->username = 'john';
        $form->email = 'john@email.com';
        $form->password = '12345678';

        $this->assertTrue($form->validate());
        $this->assertEmpty($form->errors);
    }

    public function testUsernameIsRequired(): void
    {
        $form = new RegisterForm();
        $form->email = 'john@email.com';
        $form->password = '12345678';

        $this->assertFalse($form->validate());
        $this->assertArrayHasKey('username', $form->errors);
    }

    public function testEmailIsRequired(): void
    {
        $form = new RegisterForm();
        $form->username = 'john';
        $form->password = '12345678';

        $this->assertFalse($form->validate());
        $this->assertArrayHasKey('email', $form->errors);
    }

    public function testPasswordIsRequired(): void
    {
        $form = new RegisterForm();
        $form->username = 'john';
        $form->email = 'john@email.com';

        $this->assertFalse($form->validate());
        $this->assertArrayHasKey('password', $form->errors);
    }

    public function testEmailMustBeValid(): void
    {
        $form = new RegisterForm();
        $form->username = 'john';
        $form->email = 'invalid-email';
        $form->password = '12345678';

        $this->assertFalse($form->validate());
        $this->assertArrayHasKey('email', $form->errors);
    }


    public function testUsernameMustHaveAtLeast3Characters(): void
    {
        $form = new RegisterForm();
        $form->username = 'jo';
        $form->email = 'john@email.com';
        $form->password = '12345678';

        $this->assertFalse($form->validate());
        $this->assertArrayHasKey('username', $form->errors);
    }

    public function testUsernameCannotExceed100Characters(): void
    {
        $form = new RegisterForm();
        $form->username = str_repeat('a', 101);
        $form->email = 'john@email.com';
        $form->password = '12345678';

        $this->assertFalse($form->validate());
        $this->assertArrayHasKey('username', $form->errors);
    }

    public function testPasswordMustHaveAtLeast8Characters(): void
    {
        $form = new RegisterForm();
        $form->username = 'john';
        $form->email = 'john@email.com';
        $form->password = '1234567';

        $this->assertFalse($form->validate());
        $this->assertArrayHasKey('password', $form->errors);
    }

    public function testUsernameAndEmailAreTrimmed(): void
    {
        $form = new RegisterForm();
        $form->username = '  john  ';
        $form->email = '  john@email.com  ';
        $form->password = '12345678';

        $this->assertTrue($form->validate());
        $this->assertEquals('john', $form->username);
        $this->assertEquals('john@email.com', $form->email);
    }













}
