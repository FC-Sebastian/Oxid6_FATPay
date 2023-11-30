<?php

namespace Fatchip\FATPay\Tests\Unit\extend\Application\Model;

use \Fatchip\FATPay\extend\Application\Model\Payment;

class PaymentTest extends \OxidEsales\TestingLibrary\UnitTestCase
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

    public function testFcHasFatPay()
    {
        $oPayment = new Payment();
        $this->assertEquals(false, $oPayment->fcHasFatPay());

        $oDb = $this->getDb();
        $oDb->execute("INSERT INTO oxpayments (OXID) VALUES ('fatpay')");

        $this->assertEquals(true, $oPayment->fcHasFatPay());
    }

    public function testFcCreateFatPayPayment()
    {
        $oDb = $this->getDb();
        $oPayment = new Payment();
        $this->assertEquals(false, $oPayment->fcHasFatPay());

        $oPayment->fcCreateFatPayPayment();

        $oPayment = new Payment();
        $this->assertEquals(true, $oPayment->fcHasFatPay());

        $this->assertEquals(
            $oDb->getOne("SELECT COUNT(OXID) FROM oxdeliveryset"),
            $oDb->getOne("SELECT COUNT(OXID) FROM oxobject2payment WHERE OXPAYMENTID = 'fatpay'")
        );

        $oLang = \OxidEsales\Eshop\Core\Registry::getLang();
        foreach ($oLang->getLanguageArray() as $aLang) {
            $oPayment->loadInLang($aLang->id, 'fatpay');
            $this->assertEquals('FATPay', $oPayment->oxpayments__oxdesc->value);
        }
    }

    public function testFatPayActivation()
    {
        $oPayment = new Payment();
        $oPayment->fcCreateFatPayPayment();
        $oDb = $this->getDb();

        $oPayment->fcSetFatPayInActive();
        $this->assertEquals(0, $oDb->getOne("SELECT oxactive FROM oxpayments WHERE oxid='fatpay'"));

        $oPayment->fcSetFatPayActive();
        $this->assertEquals(1, $oDb->getOne("SELECT oxactive FROM oxpayments WHERE oxid='fatpay'"));
    }
}