<?php

use Fatchip\FATPay\Application\Model\ApiRequest;

class ApiRequestTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    /**
     * @dataProvider getApiPostResponseProvider
     *
     * @param $ch
     * @param $aResponse
     * @param $iErr
     * @param $aE
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testGetApiPostResponse($ch, $aResponse, $iErr, $aE)
    {
        $oApi = $this
            ->getMockBuilder(ApiRequest::class)
            ->onlyMethods(['getApi','setApiOption','executeApiRequest','getApiError','getApiErrorCode','logApiError'])
            ->getMock();
        $oApi->method('getApi')->willReturn($ch);
        $oApi->method('executeApiRequest')->willReturn($aResponse);
        $oApi->method('getApiErrorCode')->willReturn($iErr);

        $oOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);

        $this->assertEquals($aE, $oApi->getApiPostResponse(420.69, $oOrder));
    }

    public function getApiPostResponseProvider()
    {
        return [
            [true, json_encode(['successful' => 'response']), 0, ['successful' => 'response']],
            [false, '', 0, ['status' => 'ERROR', 'errormessage' => 'COULDNT_CONNECT_TO_API']],
            [true, '', 1, ['status' => 'ERROR', 'errormessage' => 'COULDNT_CONNECT_TO_API']],
        ];
    }

    public function testGetApiGetResponse($ch, $aResponse, $iErr, $aE)
    {
        $oApi = $this
            ->getMockBuilder(ApiRequest::class)
            ->onlyMethods(['getApi','setApiOption','executeApiRequest','getApiError','getApiErrorCode','logApiError'])
            ->getMock();
        $oApi->method('getApi')->willReturn($ch);
        $oApi->method('executeApiRequest')->willReturn($aResponse);
        $oApi->method('getApiErrorCode')->willReturn($iErr);

        if (!$ch || !$iErr) {
            $oApi->expects($this->once())->method('logApiError');
        }

        $this->assertEquals($aE, $oApi->getApiGetResponse(''));
    }

    public function getApiGetResponseProvider()
    {
        return [
            [true, ['success'],0,['success']],
            [false, '',0,json_encode(['status' => 'ERROR', 'errormessage' => 'COULDNT_CONNECT_TO_API'])],
            [true, '',1,json_encode(['status' => 'ERROR', 'errormessage' => 'COULDNT_CONNECT_TO_API'])],
        ];
    }
}