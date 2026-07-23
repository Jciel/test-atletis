<?php

namespace app\models\forms;

use app\models\cast\MoneyCast;
use app\services\MoneyFactory;
use yii\base\Model;

class ExpenseForm extends Model
{
    public ?int $category_id = null;
    public string $description = '';
    public string $amount = '';
    public string $expense_date = '';
    public string $currency = 'BRL';

    public function rules(): array
    {
        return [
            [['category_id', 'description', 'amount', 'expense_date'], 'required'],
            [['category_id'], 'integer'],
            [['amount'], 'match', 'pattern' => '/^\d+([.,]\d{1,2})?$/',],
            [['amount'], 'validateAmount'],
            [['currency'], 'string', 'length' => 3],
            [['description'], 'string', 'max' => 255],
            [['expense_date'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function validateAmount(string $attribute): void
    {
        $money = MoneyFactory::fromDecimal($this->$attribute);

        if ($money->getAmount() <= 0) {
            $this->addError($attribute, 'Amount must be greater than zero.');
        }
    }
}
