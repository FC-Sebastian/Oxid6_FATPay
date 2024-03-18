<?php

namespace Fatchip\FATPay\Application\Model;

class FatPayHelper
{
    const aFatpayPayments = [
        ['id' => 'fatpay', 'desc' => 'FATPay', 'toAmount' => 1000000],
        ['id' => 'fatredirect', 'desc' => 'FATRedirect', 'toAmount' => 1000000]
    ];

    public static function isFatPayPayment($sPayment) {
        foreach (self::aFatpayPayments as $aFatpayPayment) {
            if ($aFatpayPayment['id'] === $sPayment) {
                return true;
            }
        }
        return false;
    }

    public static function isFatRedirect($sPayment) {
        return $sPayment === self::aFatpayPayments[1]['id'];
    }
}