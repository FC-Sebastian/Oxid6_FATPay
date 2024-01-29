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
}