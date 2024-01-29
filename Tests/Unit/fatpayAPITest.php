<?php

namespace Fatchip\FATPay\Tests\Unit;

define('PHP_UNIT', true);
include_once __DIR__ . '/../../Api/FatpayAPI.php';

class fatpayAPITest extends \PHPUnit\Framework\TestCase
{
    public function testValidatePayment()
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $oApi = new \FatpayApi();
        $oApi->sDb = 'fatpay_test';

        $_POST['data'] = json_encode(
            [
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
                'shipping_lastname' => 'approved'
            ]);
        $this->expectOutputString(
            json_encode(['status' => 'APPROVED'])
            .json_encode(['status' => 'ERROR', 'errormessage' => 'no failures allowed'])
        );
        $oApi->validatePayment();

        $oConn = new \mysqli($oApi->sServer, $oApi->sUser, $oApi->sPassword);
        $oResult = $oConn->query("SHOW DATABASES LIKE '$oApi->sDb'");
        $this->assertEquals(1, $oResult->num_rows);
        $oConn->close();

        $oConn = new \mysqli($oApi->sServer, $oApi->sUser, $oApi->sPassword, $oApi->sDb);
        $oResult = $oConn->query("SHOW TABLES LIKE '$oApi->sTable'");
        $this->assertEquals(1, $oResult->num_rows);
        $oConn->close();

        $_POST['data'] = json_encode(
            [
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
                'shipping_lastname' => 'failed'
            ]);
        $oApi->validatePayment();

        $oConn = new \mysqli($oApi->sServer, $oApi->sUser, $oApi->sPassword, $oApi->sDb);
        $oResult = $oConn->query("SELECT COUNT(id) FROM $oApi->sTable");
        $this->assertEquals(2, $oResult->fetch_array(MYSQLI_NUM)[0]);
        $oConn->query("DROP DATABASE $oApi->sDb");
        $oConn->close();
    }


}