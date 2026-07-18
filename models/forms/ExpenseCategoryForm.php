<?php

namespace app\models\forms;

use yii\base\Model;

class ExpenseCategoryForm extends Model
{
    public string $name = '';
    public string $slug = '';

    public function rules(): array
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'max' => 255],
            ['slug', 'string', 'max' => 255],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Name',
            'slug' => 'Slug',
        ];
    }
}
