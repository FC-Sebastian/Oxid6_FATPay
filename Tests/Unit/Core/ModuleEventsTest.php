<?php

namespace Fatchip\FATPay\Tests\Unit\Core;

use \Fatchip\FATPay\Core\ModuleEvents;

class ModuleEventsTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    public function setUp(): void
    {
        $oDb = $this->getDb();
        $oDb->execute("DELETE FROM oxpayments WHERE OXID = 'fatpay'");
        $oDb->execute("DELETE FROM oxobject2payment WHERE oxpaymentid = 'fatpay'");
    }

    public function tearDown(): void
    {
        $oDb = $this->getDb();
        $oDb->execute("DELETE FROM oxpayments WHERE OXID = 'fatpay'");
        $oDb->execute("DELETE FROM oxobject2payment WHERE oxpaymentid = 'fatpay'");
    }

    public function testOnActivate()
    {
        $oDb = $this->getDb();
        $oEvents = new ModuleEvents();
        $oEvents::onActivate();

        $this->assertEquals(1, $oDb->getOne("SELECT oxactive FROM oxpayments WHERE oxid = 'fatpay'"));
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

        $this->assertEquals(0, $oDb->getOne("SELECT oxactive FROM oxpayments WHERE oxid = 'fatpay'"));
    }
}