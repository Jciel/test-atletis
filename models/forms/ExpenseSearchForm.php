<?php

namespace app\models\forms;

use yii\base\Model;

class ExpenseSearchForm extends Model
{
    public ?int $category_id = null;
    public ?int $month = null;
    public ?int $year = null;
    public string $sort = 'desc';
    public int $page = 1;
    public int $per_page = 10;

    public function rules(): array
    {
        return [
            [['category_id', 'month', 'year', 'page', 'per_page'], 'integer'],
            [['sort'], 'in', 'range' => ['asc', 'desc']],
            [['month'], 'integer', 'min' => 1, 'max' => 12],
            [['year'], 'integer', 'min' => 2000, 'max' => 2100],
            [['page'], 'integer', 'min' => 1],
            [['per_page'], 'integer', 'min' => 1, 'max' => 100],
        ];
    }

    public function hasCategory(): bool
    {
        return $this->category_id !== null;
    }

    public function hasPeriod(): bool
    {
        return $this->month !== null && $this->year !== null;
    }
}
