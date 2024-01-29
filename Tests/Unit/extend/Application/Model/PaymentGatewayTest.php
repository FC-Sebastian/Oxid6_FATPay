<?php

namespace Fatchip\Fatpay\Tests\Unit\extend\Application\Model;

use Fatchip\FATPay\extend\Application\Model\PaymentGateway;

class PaymentGatewayTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    public function setUp(): void
    {
        $oDb = $this->getDb();
        $oDb->execute("INSERT INTO oxorder (oxid) VALUES ('mockOrder')");
    }

    public function tearDown(): void
    {
        $oDb = $this->getDb();
        $oDb->execute("DELETE FROM oxorder WHERE OXID = 'mockOrder'");
    }

    /**
     * @dataProvider executePaymentProvider
     */
    public function testExecutePayment($aResponse, $blExpected)
    {
        $oUserPayment = oxNew(\OxidEsales\Eshop\Application\Model\UserPayment::class);
        $oUserPayment->oxuserpayments__oxpaymentsid->value = 'fatpay';
        $oOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
        $oOrder->load('mockOrder');

        $oPaymentGateway = $this->getMockBuilder(PaymentGateway::class)->onlyMethods(['fcRedirect', 'fcGetApiResponse'])->getMock();
        $oPaymentGateway->setPaymentParams($oUserPayment);

        $oPaymentGateway->method('fcGetApiResponse')->willReturn($aResponse);
        $this->assertEquals($blExpected ,$oPaymentGateway->executePayment(12.3, $oOrder));
    }

    public function executePaymentProvider()
    {
        return[
            [['status' => 'ERROR'], false],
            [['status' => 'APPROVED'], true]
        ];
    }

    public function testExecutePaymentRedirect()
    {
        $oUserPayment = oxNew(\OxidEsales\Eshop\Application\Model\UserPayment::class);
        $oUserPayment->oxuserpayments__oxpaymentsid->value = 'fatredirect';
        $oOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
        $oOrder->load('mockOrder');

        $oPaymentGateway = $this->getMockBuilder(PaymentGateway::class)->onlyMethods(['fcRedirect', 'fcGetApiResponse'])->getMock();
        $oPaymentGateway->method('fcRedirect')->will($this->returnCallback(function (){
            echo 'redirected';
        }));
        $oPaymentGateway->setPaymentParams($oUserPayment);

        $oPaymentGateway->method('fcGetApiResponse')->willReturn(['status' => 'REDIRECT']);
        $this->expectOutputString('redirected');
        $oPaymentGateway->executePayment(12.3, $oOrder);
    }
}