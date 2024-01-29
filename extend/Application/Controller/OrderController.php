<?php

namespace Fatchip\FATPay\extend\Application\Controller;

use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Core\Registry;

class OrderController extends OrderController_parent
{
    /**
     * Loads order from orderid request parameter sets oxtransstatus of order to 'OK'
     * then redirects to thankyou controller
     *
     * @return void
     */
    public function fcFinalizeFatRedirect()
    {
        $sOxid = Registry::getRequest()->getRequestParameter('orderId');
        if ($sOxid) {
            $oOrder = oxNew(Order::class);
            $oOrder->load($sOxid);
            $oOrder->fcSetOrderNumber();
            $oOrder->assign(['oxtransstatus' => 'OK']);
            $oOrder->save();

            $this->getSession()->setBasket($oOrder->getBasket());
            $this->fcRedirect();
        }
    }

    /**
     * Redirects to thankyou controller
     *
     * @return void
     */
    public function fcRedirect()
    {
        Registry::getUtils()->redirect($this->getConfig()->getCurrentShopUrl() . '?cl=thankyou');
    }
}