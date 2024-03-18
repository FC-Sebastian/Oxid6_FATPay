<?php

namespace Fatchip\FATPay\Api;

include './ApiCurlHelper.php';

class FatRedirectAjax
{
    protected $sApiUrl = 'http://localhost/modules/fc/fatpay/Api/FatpayAPI.php';
    protected $ch = null;

    /**
     * Validates whether user is 18y or older, updates transaction if yes echoes error if no
     *
     * @return void
     */
    public function validateTransaction()
    {
        $iBday = $this->getPostParam('bday');
        if (strtotime('+18 years',strtotime($iBday)) < time()) {
            echo $this->updateTransaction();
        } else {
            echo json_encode(['status' => 'ERROR', 'errormessage' => 'You must be of age to pay with FatRedirect']);
        }
    }

    /**
     * updates transaction status using curl
     *
     * @return bool|string
     */
    public function updateTransaction() {
        $oCurlHelper = $this->getCurlHelper();
        return $oCurlHelper->executeApiPutRequest($this->sApiUrl, $this->getPostParam('transaction'), ['Content-Type: application/json']);
    }

    public function getCurlHelper()
    {
        return new ApiCurlHelper();
    }

    /**
     * Gets post param via param key
     *
     * @param $sParam
     * @return false|mixed
     */
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
