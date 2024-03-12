<?php

namespace Fatchip\FATPay\Api;

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
        $this->ch = $this->getApi();

        if (!$this->ch) {
            return json_encode(['status' => 'ERROR', 'errormessage' => 'Couldn\'t connect to API']);
        }

        $this->setApiOption(CURLOPT_RETURNTRANSFER, true);
        $this->setApiOption(CURLOPT_CUSTOMREQUEST, 'PUT');
        $this->setApiOption(CURLOPT_POSTFIELDS, $this->getPostParam('transaction'));
        $this->setApiOption(CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $aResponse = $this->executeApiRequest();

        if ($this->getApiError()) {
            return json_encode(['status' => 'ERROR', 'errormessage' => 'There was an error when communicating with the API']);
        }

        return $aResponse;
    }

    /**
     * Returns cURL handle
     *
     * @return false|CurlHandle
     */
    public function getApi()
    {
        return curl_init($this->sApiUrl);
    }

    /**
     * Sets cURL option
     *
     * @param $sName
     * @param $value
     * @return void
     */
    public function setApiOption($sName, $value)
    {
        curl_setopt($this->ch, $sName, $value);
    }

    /**
     * Executes cURL request
     *
     * @return bool|string
     */
    public function executeApiRequest()
    {
        return curl_exec($this->ch);
    }

    /**
     * Returns cURL error code
     *
     * @return int
     */
    public function getApiError()
    {
        return curl_errno($this->ch);
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


