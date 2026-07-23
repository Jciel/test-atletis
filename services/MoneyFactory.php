<?php

namespace app\services;

use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Money;
use Money\Parser\DecimalMoneyParser;

final class MoneyFactory
{
    private static ?ISOCurrencies $currencies = null;
    private static ?DecimalMoneyParser $parser = null;

    public static function fromDecimal(string $amount, string $currency = 'BRL'): Money
     {
        $amount = str_replace(',', '.', trim($amount));
        return self::parser()->parse($amount, new Currency($currency));
    }

    private static function parser(): DecimalMoneyParser
    {
        return self::$parser ??= new DecimalMoneyParser(self::currencies());
    }

    private static function currencies(): ISOCurrencies
    {
        return self::$currencies ??= new ISOCurrencies();
    }
}
