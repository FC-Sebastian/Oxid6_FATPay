<?php

namespace Fatchip\FATPay\extend\Application\Model;

use OxidEsales\Eshop\Application\Model\UserPayment;
use OxidEsales\Eshop\Application\Model\Basket;
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
        Registry::getLogger()->error('ORDER CHECKING FINALIZE FLAG');
        if (Registry::getSession()->getVariable('fatRedirectVerified') === true) {
            Registry::getLogger()->error('ORDER SHOULD NOT APPEAR');
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

    protected function _executePayment(Basket $oBasket, $oUserpayment)
    {
        if ($this->blFcFinalizeRedirect === false) {
            return parent::_executePayment($oBasket, $oUserpayment);
        }

        if ($this->blFcFinalizeRedirect === true) {
            // Finalize order would set a new incremented order-nr if already filled
            // Doing this to prevent this, oxordernr will be filled again in _setNumber
            $this->iFcOrderNr = $this->oxorder__oxordernr->value;
            $this->oxorder__oxordernr->value = "";
        }
        return true;
    }

    protected function _setPayment($sPaymentid)
    {
        if ($this->blFcFinalizeRedirect === false) {
            return parent::_setPayment($sPaymentid);
        }
        $oUserpayment = oxNew(UserPayment::class);
        $oUserpayment->load($this->oxorder__oxpaymentid->value);
        return $oUserpayment;
    }
}