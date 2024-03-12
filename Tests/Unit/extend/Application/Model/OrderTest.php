<?php

namespace Fatchip\Fatpay\Tests\Unit\extend\Application\Model;

class OrderTest extends \OxidEsales\TestingLibrary\UnitTestCase
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

    public function testFcSetOrderNumber()
    {
        $oOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
        $oOrder->load('mockOrder');

        self::assertEmpty($oOrder->oxorder__oxordernr->value);
        $oOrder->fcSetOrderNumber();
        self::assertNotEmpty($oOrder->oxorder__oxordernr->value);
    }

    /**
     * @dataProvider finalizeOrderProvider
     *
     * @param $blRedirected
     * @return void
     */
    public function testFinalizeOrder($blRedirected)
    {
        $oOrder = $this
            ->getMockBuilder(\OxidEsales\Eshop\Application\Model\Order::class)
            ->onlyMethods(['_setNumber', '_loadFromBasket', '_executePayment', '_setPayment'])
            ->getMock();
        $oOrder->load('mockOrder');

        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable('fatRedirectVerified', $blRedirected);

        if (!$blRedirected) {
            $oOrder->expects($this->once())->method('_setNumber');
            $oOrder->expects($this->once())->method('_loadFromBasket');
            $oOrder->expects($this->once())->method('_executePayment');
            $oOrder->expects($this->once())->method('_setPayment');
        } else {
            $oOrder->expects($this->never())->method('_setNumber');
            $oOrder->expects($this->never())->method('_loadFromBasket');
            $oOrder->expects($this->never())->method('_executePayment');
            $oOrder->expects($this->never())->method('_setPayment');
        }
    }

    public function finalizeOrderProvider()
    {
        return [
            //testing no redirect
            false,
            //testing redirect
            true
        ];
    }
}