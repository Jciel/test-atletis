<?php

namespace app\services;

use Money\Currencies\ISOCurrencies;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;

final class MoneyFormatter
{
    private static ?DecimalMoneyFormatter $formatter = null;

    public static function decimal(Money $money): string
    {
        return self::formatter()->format($money);
    }

    private static function formatter(): DecimalMoneyFormatter
    {
        return self::$formatter ??= new DecimalMoneyFormatter(new ISOCurrencies());
    }
}
