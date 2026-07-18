<?php

namespace app\resources;

use app\resources\BaseResource;

class ExpenseResource extends BaseResource
{
    public static function make(object $model): array
    {
        return [
            'id' => (int)$model->id,
            'description' => $model->description,
            'amount' => (float)$model->amount,
            'expense_date' => $model->expense_date,
            'category' => [
                'id' => (int)$model->category->id,
                'name' => $model->category->name,
                'slug' => $model->category->slug,
            ],
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at,
        ];
    }
}
