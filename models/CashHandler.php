<?php


namespace app\models;


class CashHandler
{

    public static function toDBCash($amount): int
    {
        // заменю запятую на точку, если она есть
        $amount = str_ireplace(',', '.', $amount);
        $numAmount = (float) $amount;
        return (int) round($numAmount * 100, 0, PHP_ROUND_HALF_UP);
    }

    /**
     * @param string $amount
     * @return string
     */
    public static function toSmooth(string $amount): string
    {
        return (int) ($amount / 100) . ' руб. ' . $amount % 100 . ' коп.';
    }

    public static function toFloat(string $amount)
    {
        return round($amount / 100, 2, PHP_ROUND_HALF_UP);
    }
}