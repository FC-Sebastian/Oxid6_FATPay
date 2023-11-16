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

    public static function insertFatPayPayment()
    {
        $oPayment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);
        if ($oPayment->fcHasFatPay() === false) {
            $oPayment->fcCreateFatPayPayment();
        } else {
            $oPayment->fcSetFatPayActive();
        }
    }

    public static function setFatPayInactive()
    {
        $oPayment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);
        if ($oPayment->fcHasFatPay() === true) {
            $oPayment->fcSetFatPayInActive();
        }
    }
}