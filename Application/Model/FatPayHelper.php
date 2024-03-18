<?php

namespace Fatchip\FATPay\Application\Model;

trait FatPayHelper
{
    protected $aFatpayPayments = [
        ['id' => 'fatpay', 'desc' => 'FATPay', 'toAmount' => 1000000],
        ['id' => 'fatredirect', 'desc' => 'FATRedirect', 'toAmount' => 1000000]
    ];

    public function isFatPayPayment($sPayment) {
        foreach ($this->aFatpayPayments as $aFatpayPayment) {
            if ($aFatpayPayment['id'] === $sPayment) {
                return true;
            }
        }
        return false;
    }

    public function isFatRedirect($sPayment) {
        return $sPayment === $this->aFatpayPayments[1]['id'];
    }
}