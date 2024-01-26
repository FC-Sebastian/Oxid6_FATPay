<?php

namespace Fatchip\FATPay\extend\Application\Controller;

use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Core\Registry;

class OrderController
{
    public function fcFinalizeFatReditect()
    {
        $sOxid = Registry::getRequest()->getRequestParameter('orderId');
        if ($sOxid) {
            $oOrder = oxNew(Order::class);
            $oOrder->load($sOxid);
            $oOrder->fcSetOrderNumber();
            $oOrder->assign(['oxtransstatus' => 'OK']);
            $oOrder->save();

            $this->getSession()->setBasket($oOrder->getBasket());
            Registry::getUtils()->redirect($this->getConfig()->getCurrentShopUrl() . '&cl=thankyou');
        }
    }
}