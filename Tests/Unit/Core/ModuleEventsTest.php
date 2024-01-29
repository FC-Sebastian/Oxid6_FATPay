<?php

namespace Fatchip\FATPay\Tests\Unit\Core;

use \Fatchip\FATPay\Core\ModuleEvents;

class ModuleEventsTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    public function setUp(): void
    {
        $oDb = $this->getDb();
        $oDb->execute("DELETE FROM oxpayments WHERE OXID = 'fatpay' OR OXID = 'fatredirect'");
        $oDb->execute("DELETE FROM oxobject2payment WHERE OXPAYMENTID = 'fatpay' OR OXPAYMENTID = 'fatredirect'");
    }

    public function tearDown(): void
    {
        $oDb = $this->getDb();
        $oDb->execute("DELETE FROM oxpayments WHERE OXID = 'fatpay' OR OXID = 'fatredirect'");
        $oDb->execute("DELETE FROM oxobject2payment WHERE OXPAYMENTID = 'fatpay' OR OXPAYMENTID = 'fatredirect'");
    }

    public function testOnActivate()
    {
        $oDb = $this->getDb();
        $oEvents = new ModuleEvents();
        $oEvents::onActivate();

        $this->assertEquals(1, $oDb->getOne("SELECT OXACTIVE FROM oxpayments WHERE OXID = 'fatpay'"));
        $this->assertEquals(
            $oDb->getOne("SELECT COUNT(OXID) FROM oxdeliveryset"),
            $oDb->getOne("SELECT COUNT(OXID) FROM oxobject2payment WHERE OXPAYMENTID = 'fatpay'")
        );
    }

    public function testOnDeactivate()
    {
        $oDb = $this->getDb();
        $oEvents = new ModuleEvents();
        $oEvents::onActivate();
        $oEvents::onDeactivate();

        $this->assertEquals(0, $oDb->getOne("SELECT OXACTIVE FROM oxpayments WHERE OXID = 'fatpay'"));
    }
}