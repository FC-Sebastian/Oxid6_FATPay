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
        $oPayment = oxNew(\Fatchip\extend\Application\Model\Payment::class);
        if ($oPayment->fcHasFatPay() === false) {
            $oPayment->setId('fatpay');
            $oPayment->oxpayments__oxdesc = new \OxidEsales\Eshop\Core\Field('FAT-Pay');
            $oPayment->oxpayments__oxtoamount = new \OxidEsales\Eshop\Core\Field(1000000);
        }
    }

    public static function setFatPayInactive()
    {
        $oPayment = oxNew(\Fatchip\extend\Application\Model\Payment::class);
        if ($oPayment->fcHasFatPay() === true) {
            $oPayment->load('fatpay');
        }
    }
}