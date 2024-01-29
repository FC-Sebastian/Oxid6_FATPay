<?php

namespace Fatchip\FATPay\Tests\Unit;

define('PHP_UNIT', true);
include_once __DIR__ . '/../../Api/FatpayAPI.php';

class FatpayAPITest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider validatePaymentProvider
     */
    public function testValidatePayment($aData, $sExpected)
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $oApi = $this->getMockBuilder(\Fatchip\FATPay\Api\FatpayApi::class)->onlyMethods(['getPostData'])->getMock();
        $oApi->method('getPostData')->willReturn($aData);
        $oApi->sDb = 'fatpay_test';

        //asserting data was evaluated correctly
        $this->expectOutputString($sExpected);
        $oApi->validatePayment();

        //asserting db was created
        $oConn = new \mysqli($oApi->sServer, $oApi->sUser, $oApi->sPassword);
        $oResult = $oConn->query("SHOW DATABASES LIKE '$oApi->sDb'");
        $this->assertEquals(1, $oResult->num_rows);
        $oConn->close();

        //asserting table was created
        $oConn = new \mysqli($oApi->sServer, $oApi->sUser, $oApi->sPassword, $oApi->sDb);
        $oResult = $oConn->query("SHOW TABLES LIKE '$oApi->sTable'");
        $this->assertEquals(1, $oResult->num_rows);
        $oConn->close();

        //asserting log entry was created
        $oConn = new \mysqli($oApi->sServer, $oApi->sUser, $oApi->sPassword, $oApi->sDb);
        $oResult = $oConn->query("SELECT COUNT(id) FROM $oApi->sTable");
        $this->assertEquals(1, $oResult->fetch_array(MYSQLI_NUM)[0]);

        $oConn->query("DROP DATABASE $oApi->sDb");
        $oConn->close();
    }

    public function validatePaymentProvider()
    {
        return [
            [[
                'shopsystem' => 'filler',
                'shopversion' => 'filler',
                'moduleversion' => 'filler',
                'language' => 'fi',
                'billing_firstname' => 'filler',
                'billing_street' => 'filler',
                'billing_zip' => 'filler',
                'billing_city' => 'filler',
                'billing_country' => 'filler',
                'shipping_firstname' => 'filler',
                'shipping_street' => 'filler',
                'shipping_zip' => 'filler',
                'shipping_city' => 'filler',
                'shipping_country' => 'filler',
                'email' => 'filler',
                'customer_nr' => 'filler',
                'currency' => 'fil',
                'order_sum' => '0.00',
                'billing_lastname' => 'approved',
                'shipping_lastname' => 'approved',
                'payment_type' => 'fatpay'
            ], json_encode(['status' => 'APPROVED'])],
            [[
                'shopsystem' => 'filler',
                'shopversion' => 'filler',
                'moduleversion' => 'filler',
                'language' => 'fi',
                'billing_firstname' => 'filler',
                'billing_street' => 'filler',
                'billing_zip' => 'filler',
                'billing_city' => 'filler',
                'billing_country' => 'filler',
                'shipping_firstname' => 'filler',
                'shipping_street' => 'filler',
                'shipping_zip' => 'filler',
                'shipping_city' => 'filler',
                'shipping_country' => 'filler',
                'email' => 'filler',
                'customer_nr' => 'filler',
                'currency' => 'fil',
                'order_sum' => '0.00',
                'billing_lastname' => 'failed',
                'shipping_lastname' => 'failed',
                'payment_type' => 'fatpay'
            ], json_encode(['status' => 'ERROR', 'errormessage' => 'no failures allowed'])],
            [[
                'shopsystem' => 'filler',
                'shopversion' => 'filler',
                'moduleversion' => 'filler',
                'language' => 'fi',
                'billing_firstname' => 'filler',
                'billing_street' => 'filler',
                'billing_zip' => 'filler',
                'billing_city' => 'filler',
                'billing_country' => 'filler',
                'shipping_firstname' => 'filler',
                'shipping_street' => 'filler',
                'shipping_zip' => 'filler',
                'shipping_city' => 'filler',
                'shipping_country' => 'filler',
                'email' => 'filler',
                'customer_nr' => 'filler',
                'currency' => 'fil',
                'order_sum' => '0.00',
                'billing_lastname' => 'approved',
                'shipping_lastname' => 'approved',
                'payment_type' => 'fatredirect'
            ], json_encode(['status' => 'REDIRECT'])]
        ];
    }
}