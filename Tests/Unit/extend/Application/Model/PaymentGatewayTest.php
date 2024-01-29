<?php

namespace Fatchip\Fatpay\Tests\Unit\extend\Application\Model;

use OxidEsales\Eshop\Application\Model\Payment;

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

    public function testExecutePayment()
    {
        $oOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
        $oOrder->load('mockOrder');

        $oPaymentGateway = $this->getMockBuilder(Payment::class)->onlyMethods(['fcRedirect', 'fcGetApiResponse'])->getMock();

        $oPaymentGateway->method('fcRedirect')->will($this->returnCallback(function (){
            echo 'redirected';
        }));
        $oPaymentGateway->method('fcGetApiResponse')->willReturn(['status' => 'APPROVED']);
        $this->assertTrue($oPaymentGateway->executePayment(12.3, $oOrder));

        $oPaymentGateway->method('fcGetApiResponse')->willReturn(['status' => 'ERROR']);
        $this->assertFalse($oPaymentGateway->executePayment(12.3, $oOrder));

        $oPaymentGateway->method('fcGetApiResponse')->willReturn(['status' => 'REDIRECT']);
        $this->expectOutputString('redirected');
        $oPaymentGateway->executePayment(12.3, $oOrder);
    }
}