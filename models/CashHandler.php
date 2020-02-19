<?php


namespace app\models;


class CashHandler
{

    public static function toDBCash($amount)
    {
        // заменю запятую на точку, если она есть
        $amount = str_ireplace(',', '.', $amount);
        $numAmount = (float) $amount;
        return (int) ($numAmount * 100);
    }

    /**
     * @param string $amount
     * @return string
     */
    public static function toSmooth(string $amount)
    {
        return (int) ($amount / 100) . ' руб. ' . $amount % 100 . ' коп.';
    }

    public static function toFloat(string $amount)
    {
        return round($amount / 100, 2);
    }
}