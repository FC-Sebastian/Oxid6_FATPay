<?php

namespace Fatchip\FATPay\Tests\Unit\Api;

use Fatchip\FATPay\Api\FatpayApi;

define('PHP_UNIT', true);
include_once __DIR__ . '/../../../Api/FatpayAPI.php';

class FatpayAPITest extends \PHPUnit\Framework\TestCase
{

    public function tearDown(): void
    {
        $oApi = new FatpayApi();
        $oApi->sDb = 'fatpay_test';

        $oConn = new \mysqli($oApi->sServer, $oApi->sUser, $oApi->sPassword, $oApi->sDb);
        $oConn->query("DROP DATABASE $oApi->sDb");
        $oConn->close();
    }

    /**
     * @dataProvider validatePaymentProvider
     */
    public function testValidatePayment($aData, $sExpected)
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $oApi = $this->getMockBuilder(FatpayApi::class)->onlyMethods(['getPhpInput'])->getMock();
        $oApi->method('getPhpInput')->willReturn($aData);
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
    }

    /**
     * @dataProvider getTransactionStatusProvider
     */
    public function testGetTransactionStatus($sGet, $sQuery, $sE)
    {
        $_GET['transaction'] = $sGet;
        $oApi = $this->getMockBuilder(FatpayApi::class)->onlyMethods(['terminate'])->getMock();
        $oApi->method('terminate')->willReturnCallback([$this, 'terminateDouble']);
        $oApi->sDb = 'fatpay_test';

        $oApi->createDb();
        $oApi->createTable();

        $oConn = new \mysqli($oApi->sServer, $oApi->sUser, $oApi->sPassword, $oApi->sDb);
        $oConn->query($sQuery);
        $oConn->close();

        //asserting output matches expected response
        $this->expectOutputString($sE);
        $oApi->getTransactionStatus();


    }

    public function testUpdateTransactionStatus($sId, $sQuery, $sE)
    {
        $oApi = $this->getMockBuilder(FatpayApi::class)->onlyMethods(['getPhpInput'])->getMock();
        $oApi->method('getPhpInput')->willReturn($sId);
        $oApi->sDb = 'fatpay_test';

        $oApi->createDb();
        $oApi->createTable();

        $oConn = new \mysqli($oApi->sServer, $oApi->sUser, $oApi->sPassword, $oApi->sDb);
        $oConn->query($sQuery);
        $oConn->close();

        //asserting output matches expected response
        $this->expectOutputString($sE);
        $oApi->updateTransactionStatus();
    }

    public function updateTransactionStatusProvider()
    {
        return [
            //testing whether an entry is updated
            [
                '1234',
                "INSERT INTO fatpay_test (transactionId, order_nr, shop, shop_version, fatpay_version, payment_type, language,payment_status) VALUES ('1234',0,'shop',0,0,'payment','lang','APPROVED')",
                json_encode(['status' => 'SUCCESS'])
            ],
            //testing non-existent entry
            [
                '12345',
                'SELECT * FROM fatpay_test',
                json_encode(['status' => 'ERROR', 'errormessage' => 'Couldn\'t find transaction'])
            ],
            //testing mysql error
            [
                5.5,
                'SELECT * FROM fatpay_test',
                json_encode(['status' => 'ERROR', 'errormessage' => 'There was an error trying to update transaction'])
            ]
        ];
    }

    public function getTransactionStatusProvider()
    {
        return [
            //testing whether the correct status is returned
            [
                '1234',
                "INSERT INTO fatpay_test (transactionId, order_nr, shop, shop_version, fatpay_version, payment_type, language,payment_status) VALUES ('1234',0,'shop',0,0,'payment','lang','APPROVED')",
                'APPROVED'
            ],
            //testing for non-existent entry
            [
                '12345',
                "INSERT INTO fatpay_test (transactionId, order_nr, shop, shop_version, fatpay_version, payment_type, language,payment_status) VALUES ('1234',0,'shop',0,0,'payment','lang','APPROVED')",
                'TRANSACTION_ID_NOT_FOUND'
            ],
            //testing for no entry given
            [
                '',
                'SELECT * FROM fatpay_test',
                'NO_TRANSACTION_ID_GIVEN'
            ]
        ];
    }

    public function terminateDouble($sMessage)
    {
        echo $sMessage;
    }

    public function validatePaymentProvider()
    {
        return [
            //testing fatpay payment + valid name
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
            //testing invalid name
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
            //testing fatredirect payment
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