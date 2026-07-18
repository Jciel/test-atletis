<?php

namespace app\models\forms;

use yii\base\Model;
class RegisterForm extends Model
{
    public string $username = '';
    public string $email = '';
    public string $password = '';

    public function rules(): array
    {
        return [
            [['username', 'email'], 'trim'],
            [['username', 'email', 'password'], 'required'],
            ['email', 'email'],
            ['username', 'string', 'min' => 3, 'max' => 100],
            ['password', 'string', 'min' => 8],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'username' => 'Username',
            'email' => 'Email',
            'password' => 'Password',
        ];
    }
}
