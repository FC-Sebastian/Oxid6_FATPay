<?php

namespace Fatchip\FATPay\extend\Application\Model;

class Order extends Order_parent
{
    public function finalizeOrder(\OxidEsales\Eshop\Application\Model\Basket $oBasket, $oUser, $blRecalculatingOrder = false) {
        $iReturn = parent::finalizeOrder($oBasket, $oUser, $blRecalculatingOrder);

        if ($oBasket->getPaymentId() === 'fatredirect') {
            $sRedirect = \OxidEsales\Eshop\Core\Registry::getRequest()->getRequestParameter('fatredirect_url');
            if (!empty($sRedirect)) {
                $this->_setOrderStatus('NOT_FINISHED');
                header('Location: ' .$sRedirect. '?orderId=' .$this->getId(). '&orderStatus=' .$iReturn);
                exit();
            }
        }
        return $iReturn;
    }

    public function fcFinalizeFATRedirectOrder($sOxid)
    {
        $this->load($sOxid);
        $this->_setOrderStatus('OK');
    }
}