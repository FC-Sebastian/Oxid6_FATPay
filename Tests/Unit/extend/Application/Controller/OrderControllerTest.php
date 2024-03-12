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
    }

    public function tearDown(): void
    {
        $oDb = $this->getDb();
        $oDb->execute("DELETE FROM oxorder WHERE OXID = 'mockOrder'");
    }

    /**
     * @dataProvider fcFinalizeRedirectProvider
     *
     * @param $sId
     * @param $sTransaction
     * @param $aResponse
     * @param $blCancel
     * @param $blRedirect
     * @param $blExecute
     * @return void
     */
    public function testFcFinalizeFatRedirect($sId, $sTransaction, $aResponse, $blCancel, $blRedirect, $blExecute)
    {
        if ($sId && $sTransaction) {
            $oOrder = oxNew(Order::class);
            $oOrder->load($sId);
            $oOrder->oxorder__oxtransid->value = $sTransaction;
            $oOrder->save();
        }

        $oOrderController = $this
            ->getMockBuilder(OrderController::class)
            ->onlyMethods(['fcCancelCurrentOrder','fcRedirectWithError', 'getApiResponse', 'execute'])
            ->getMock();
        $oOrderController->method('getApiResponse')->willReturn($aResponse);

        if ($blCancel) {
            $oOrderController->expects($this->once())->method('fcCancelCurrentOrder');
        }
        if ($blRedirect) {
            $oOrderController->expects($this->once())->method('fcRedirectWithError');
        }
        if ($blExecute) {
            $oOrderController->expects($this->once())->method('execute');
        }

        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable('sess_challange', $sId);
        $oOrderController->fcFinalizeRedirect();
    }

    public function fcFinalizeRedirectProvider()
    {
        return [
            //testing approved response
            ['mockOrder','1234', json_encode(['status' => 'APPROVED']), false, false, true],
            //testing pending response
            ['mockOrder','1234', json_encode(['status' => 'PENDING']), true, true, false],
            //testing error response
            ['mockOrder','1234', json_encode(['status' => 'ERROR']), true, true, false],
            //testing empty transaction id
            ['mockOrder', false, false, true, true, false],
            //testing order not found
            [false, false, false, false, true, false],
        ];
    }
}