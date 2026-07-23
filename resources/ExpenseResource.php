<?php

namespace app\resources;

use app\models\cast\MoneyCast;
use app\resources\BaseResource;
use app\services\MoneyFormatter;

class ExpenseResource extends BaseResource
{
    public static function make(object $expenseModel): array
    {
        return [
            'id' => (int)$expenseModel->id,
            'description' => $expenseModel->description,
            'amount' => MoneyFormatter::decimal($expenseModel->amount),
            'currency' => $expenseModel->amount->getCurrency(),
            'expense_date' => $expenseModel->expense_date,
            'category' => [
                'id' => (int)$expenseModel->category->id,
                'name' => $expenseModel->category->name,
                'slug' => $expenseModel->category->slug,
            ],
            'created_at' => $expenseModel->created_at,
            'updated_at' => $expenseModel->updated_at,
        ];
    }
}
