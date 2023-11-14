<?php

namespace IwslibLaravel\Util;

class TaxUtil
{

    public static function calcInnerTaxAmount(int $amount, int $rate): int
    {
        $taxRate = $rate / 100;
        return intval(floor($amount / (1 + $taxRate) * $taxRate));
    }
}
