<?php

namespace Fatchip\FATPay\extend\Application\Controller;

use \OxidEsales\Eshop\Core\Registry;

class OrderController extends OrderController_parent
{
    public function render()
    {
        $sReturn = parent::render();
        $oRequest = Registry::getRequest();
        if ($oRequest->getRequestParameter('fatredirectValid', false) !== false) {
            $oOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
            $oOrder->fcFinalizeFATRedirectOrder($oRequest->getRequestParameter('orderId'));
            header('Location: '.Registry::getConfig()->getConfigParam('sShopUrl').'/index.php?cl='.$this->_getNextStep($oRequest->getRequestParameter('orderStatus')));
            exit();
        }
        return $sReturn;
    }
}