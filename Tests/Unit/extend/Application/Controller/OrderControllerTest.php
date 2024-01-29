<?php

namespace Fatchip\Fatpay\Tests\Unit\extend\Application\Controller;

use OxidEsales\Eshop\Application\Controller\OrderController;
use OxidEsales\Eshop\Application\Model\Order;

class OrderControllerTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    public function setUp(): void
    {
        $oDb = $this->getDb();
        $oDb->execute("INSERT INTO oxorder (oxid) VALUES ('mockOrder')");
        $_POST['orderId'] = 'mockOrder';
    }

    public function tearDown(): void
    {
        $oDb = $this->getDb();
        $oDb->execute("DELETE FROM oxorder WHERE OXID = 'mockOrder'");
    }

    public function testFcFinalizeFatRedirect()
    {
        $oOrderController = $this->getMockBuilder(OrderController::class)->onlyMethods(['fcRedirect'])->getMock();
        $oOrderController->fcFinalizeFatRedirect();

        $oOrder = oxNew(Order::class);
        $oOrder->load('mockOrder');

        $this->assertEquals('OK', $oOrder->oxorder__oxtransstatus->value);
    }
}