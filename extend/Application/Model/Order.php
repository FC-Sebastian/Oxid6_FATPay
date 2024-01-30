<?php

namespace Fatchip\FATPay\extend\Application\Model;

use OxidEsales\Eshop\Core\Registry;

class Order extends Order_parent
{
    protected $blFcFinalizeRedirect = false;

    protected $iFcOrderNr;

    /**
     * Sets order number
     *
     * @return void
     */
    public function fcSetOrderNumber()
    {
        if (!$this->oxorder__oxordernr->value) {
            $this->_setNumber();
        }
    }

    public function finalizeOrder(\OxidEsales\Eshop\Application\Model\Basket $oBasket, $oUser, $blRecalculatingOrder = false)
    {
        if (Registry::getSession()->getVariable('fatRedirectVerified') === true) {
            $this->blFcFinalizeRedirect = true;
        }

        return parent::finalizeOrder($oBasket, $oUser, $blRecalculatingOrder);
    }

    protected function _checkOrderExist($sOxId = null)
    {
        if ($this->blFcFinalizeRedirect === true) {
            return false;
        }
        return parent::_checkOrderExist($sOxId);
    }

    protected function _setNumber()
    {
        if ($this->blFcFinalizeRedirect === false) {
            return parent::_setNumber();
        }
        $this->oxorder__oxordernr->value = $this->iFcOrderNr;
        return true;
    }

    protected function _setOrderStatus($sStatus)
    {
        if ($this->oxorder__oxpaymenttype->value == 'fatredirect' && $this->oxorder__oxtransstatus->value == "NOT_FINISHED" && $this->blFcFinalizeRedirect === false) {
            return;
        }
        parent::_setOrderStatus($sStatus);
    }

    protected function _loadFromBasket(\OxidEsales\Eshop\Application\Model\Basket $oBasket)
    {
        if ($this->blFcFinalizeRedirect === false) {
            parent::_loadFromBasket($oBasket);
            return;
        }
        $this->load(Registry::getSession()->getVariable('sess_challenge'));
    }

    protected function _updateWishlist($aArticleList, $oUser)
    {
        if ($this->oxorder__oxpaymenttype->value != 'fatredirect' || $this->blFcFinalizeRedirect === true) {
            parent::_updateWishlist($aArticleList, $oUser);
        }
    }

    protected function _updateNoticeList($aArticleList, $oUser)
    {
        if ($this->oxorder__oxpaymenttype->value != 'fatredirect' || $this->blFcFinalizeRedirect === true) {
            parent::_updateNoticeList($aArticleList, $oUser);
        }
    }

    protected function _markVouchers($oBasket, $oUser)
    {
        if ($this->oxorder__oxpaymenttype->value != 'fatredirect' || $this->blFcFinalizeRedirect === true) {
            parent::_markVouchers($oBasket, $oUser);
        }
    }

    protected function _sendOrderByEmail($oUser = null, $oBasket = null, $oPayment = null)
    {
        if ($this->oxorder__oxpaymenttype->value != 'fatredirect' || $this->blFcFinalizeRedirect === true) {
            parent::_sendOrderByEmail($oUser. $oBasket, $oPayment);
        }
    }
}