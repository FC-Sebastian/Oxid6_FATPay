<?php

namespace Fatchip\FATPay\Tests\Unit\Api;

use Fatchip\FATPay\Api\FatRedirectAjax;

class FatRedirectAjaxTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider validateTransactionProvider
     *
     * @param $sBday
     * @param $ch
     * @param $aResponse
     * @param $iError
     * @param $sE
     * @return void
     */
    public function testValidateTransaction($sBday, $ch, $aResponse, $iError, $sE)
    {
        $_POST['bday'] = $sBday;

        $oAjax = $this
            ->getMockBuilder(FatRedirectAjax::class)
            ->onlyMethods(['getApi','getApiError','setApiOption', 'executeApiRequest'])
            ->getMock();
        $oAjax->method('getApi')->willReturn($ch);
        $oAjax->method('getApiError')->willReturn($iError);
        $oAjax->method('executeApiRequest')->willReturn($aResponse);

        $this->expectOutputString($sE);
        $oAjax->validateTransaction();
    }

    public function validateTransactionProvider()
    {
        return [
            //testing successful response
            ['1999-11-21', true, json_encode(['status' => 'SUCCESS']), 0, json_encode(['status' => 'SUCCESS'])],
            //testing invalid birthday
            ['2023-11-21', true, '', 0, json_encode(['status' => 'ERROR', 'errormessage' => 'You must be of age to pay with FatRedirect'])],
            //testing connection error
            ['1999-11-21', false, '', 0, json_encode(['status' => 'ERROR', 'errormessage' => 'Couldn\'t connect to API'])],
            //testing curl_errno
            ['1999-11-21', true, '', 1, json_encode(['status' => 'ERROR', 'errormessage' => 'There was an error when communicating with the API'])]
        ];
    }
}