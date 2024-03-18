<?php

namespace Fatchip\FATPay\Application\Model;

class CurlHelper
{
    public function executeApiGetRequest($sApiUrl)
    {
        $ch = curl_init($sApiUrl);

        if (!$ch) {
            return json_encode(['status' => 'ERROR', 'errormessage' => 'COULDNT_CONNECT_TO_API']);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $aResponse = curl_exec($ch);

        if (curl_errno($ch)) {
            Registry::getLogger()->error('FatPay curl error: '.curl_error($ch));
            return json_encode(['status' => 'ERROR', 'errormessage' => 'COULDNT_CONNECT_TO_API']);
        }

        return $aResponse;
    }

    public function executeApiPostRequest($sApiUrl, $sPostData)
    {
        $ch = curl_init($sApiUrl);

        if (!$ch) {
            return ['status' => 'ERROR', 'errormessage' => 'COULDNT_CONNECT_TO_API'];
        }

        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $sPostData);
        curl_setopt($ch,CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $aResponse = curl_exec($ch);

        if (curl_errno($ch)) {
            Registry::getLogger()->error('FatPay curl error: '.curl_error($ch));
            return ['status' => 'ERROR', 'errormessage' => 'COULDNT_CONNECT_TO_API'];
        }

        return json_decode($aResponse,true);
    }
}