<?php

namespace Fatchip\FATPay\extend\Application\Model;

class Order extends Order_parent
{
    public function finalizeOrder(\OxidEsales\Eshop\Application\Model\Basket $oBasket, $oUser, $blRecalculatingOrder = false) {
        $iReturn = parent::finalizeOrder($oBasket, $oUser, $blRecalculatingOrder);

        if ($oBasket->getPaymentId() === 'fatredirect' && $iReturn === 1) {
            $sRedirect = \OxidEsales\Eshop\Core\Registry::getRequest()->getRequestParameter('fatredirect_url');
            if (!empty($sRedirect)) {
                $this->_setOrderStatus('NOT_FINISHED');
                header('Location: '.$sRedirect.'?orderId='.$this->getId());
                exit();
            }
        }
        return $iReturn;
    }
}