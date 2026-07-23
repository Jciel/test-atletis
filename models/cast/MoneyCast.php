<?php

namespace app\models\cast;

use Money\Currency;
use Money\Money;
use yii\db\ActiveRecord;

class MoneyCast implements CastInterface
{
    public function __construct(private readonly string $currencyAttribute)
    {}

    public function get(ActiveRecord $model, string $attribute, mixed $value): Money
    {
        return new Money($value, new Currency($model->{$this->currencyAttribute}));
    }

    public function set(ActiveRecord $model, string $attribute, mixed $value): array
    {
        return [$attribute => $value->getAmount(), $this->currencyAttribute => $value->getCurrency()->getCode()];
    }
}
