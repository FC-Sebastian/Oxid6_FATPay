<?php

namespace Fatchip\FATPay\Api;

class FatRedirectAjax
{
    protected $sApiUrl = 'http://localhost/modules/fc/fatpay/Api/FatpayAPI.php';

    public function validateTransaction()
    {
        $iBday = $this->getPostParam('bday');
        if (strtotime('+18 years',strtotime($iBday)) < time() || strtotime($iBday) < 0) {
            echo $this->updateTransaction();
        } else {
            echo json_encode(['status' => 'ERROR', 'errormessage' => 'You must be of age to pay with FatRedirect']);
        }
    }

    public function updateTransaction() {
        $ch = curl_init($this->sApiUrl);

        if (!$ch) {
            return json_encode(['status' => 'ERROR', 'errormessage' => 'AJAX couldn\'t connect to API']);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getPostParam('transaction'));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $aResponse = curl_exec($ch);

        if (curl_errno($ch)) {
            return json_encode(['status' => 'ERROR', 'errormessage' => 'There was an error when communication with API']);
        }

        return $aResponse;
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
    $oFAtRedirect->validateTransaction();
}


