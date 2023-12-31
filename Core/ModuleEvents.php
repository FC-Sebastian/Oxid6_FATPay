<?php

namespace Fatchip\FATPay\Core;

class ModuleEvents
{
    public static function onActivate()
    {
        self::insertFatPayPayment();
    }

    public static function onDeactivate()
    {
        self::setFatPayInactive();
    }

    protected static function insertFatPayPayment()
    {
        $oPayment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);
        if ($oPayment->fcHasFatPay() === false) {
            $oPayment->fcCreateFatPayPayments();
        } else {
            $oPayment->fcSetPaymentActive('fatpay');
            $oPayment->fcSetPaymentActive('fatredirect');
        }
    }

    protected static function setFatPayInactive()
    {
        $oPayment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);
        if ($oPayment->fcHasFatPay() === true) {
            $oPayment->fcSetPaymentInActive('fatpay');
            $oPayment->fcSetPaymentInActive('fatredirect');
        }
    }
}