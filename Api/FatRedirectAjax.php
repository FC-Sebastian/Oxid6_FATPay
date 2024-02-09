<?php

namespace Fatchip\FATPay\Api;

class FatRedirectAjax
{
    protected $sApiUrl = 'http://localhost/modules/fc/fatpay/Api/FatpayAPI.php';

    public function updateTransaction()
    {
        $ch = curl_init($this->sApiUrl);

        if (!$ch) {
            Registry::getLogger()->error('PAYMENTGATEWAY COULDNT CONNECT TO API');
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getPostParam('transaction'));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $aResponse = curl_exec($ch);

        if (curl_errno($ch)) {
            Registry::getLogger()->error('FatPay curl error: '.curl_error($ch));
            return ['status' => 'ERROR', 'errormessage' => 'could not reach FatPay API'];
        }

        return json_decode($aResponse,true);
    }

    public function getPostParam($sParam)
    {
        if (isset($_POST[$sParam])){
            return $_POST[$sParam];
        }
        return false;
    }
}

