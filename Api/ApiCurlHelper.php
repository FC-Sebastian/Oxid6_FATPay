<?php

namespace Fatchip\FATPay\Api;

class ApiCurlHelper
{
    public function executeApiPutRequest($sUrl, $sData, $aHeader)
    {
        $ch = curl_init($sUrl);

        if (!$ch) {
            return json_encode(['status' => 'ERROR', 'errormessage' => 'Couldn\'t connect to API']);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $sData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);

        if (curl_errno($ch)) {
            return json_encode(['status' => 'ERROR', 'errormessage' => 'There was an error when communicating with the API']);
        }
        return curl_exec($ch);
    }
}