<?php

namespace app\models\forms;

use yii\base\Model;

class ExpenseForm extends Model
{
    public ?int $category_id = null;
    public string $description = '';
    public ?float $amount = null;
    public string $expense_date = '';

    public function rules(): array
    {
        return [
            [['category_id', 'description', 'amount', 'expense_date'], 'required'],
            [['category_id'], 'integer'],
            [['amount'], 'number', 'min' => 0.01],
            [['description'], 'string', 'max' => 255],
            [['expense_date'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }
}
