<?php

namespace Fatchip\FATPay\Api;

class FatRedirectAjax
{
    protected $sApiUrl = 'http://localhost/modules/fc/fatpay/Api/FatpayAPI.php';

    public function updateTransaction()
    {
        $ch = curl_init($this->sApiUrl);

        if (!$ch) {
            echo 'AJAX COULDNT CONNECT TO API';
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getPostParam('transaction'));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $aResponse = curl_exec($ch);

        if (curl_errno($ch)) {
            echo json_encode(['status' => 'ERROR', 'errormessage' => curl_error($ch)]);
        }

        echo $aResponse;
    }

    public function getPostParam($sParam)
    {
        if (isset($_POST[$sParam])){
            return $_POST[$sParam];
        }
        return false;
    }
}
if (!defined('PHP_UNIT')) {
    $oFAtRedirect = new FatRedirectAjax();
    $oFAtRedirect->updateTransaction();
}


