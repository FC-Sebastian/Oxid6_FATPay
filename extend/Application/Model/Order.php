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

    /**
     * Finalizes order, sets session flag if fatredirect payment verified
     *
     * @param Basket $oBasket
     * @param $oUser
     * @param $blRecalculatingOrder
     * @return bool|int
     */
    public function finalizeOrder(\OxidEsales\Eshop\Application\Model\Basket $oBasket, $oUser, $blRecalculatingOrder = false)
    {
        if (Registry::getSession()->getVariable('fatRedirectVerified') === true) {
            $this->blFcFinalizeRedirect = true;
        }

        return parent::finalizeOrder($oBasket, $oUser, $blRecalculatingOrder);
    }

    /**
     * Returns false if fatredirect session flag is set otherwise calls parent method
     *
     * @param $sOxId
     * @return bool
     */
    protected function _checkOrderExist($sOxId = null)
    {
        if ($this->blFcFinalizeRedirect === true) {
            return false;
        }
        return parent::_checkOrderExist($sOxId);
    }

    /**
     * Sets order number and returns true if fatredirect session flag is set otherwise calls parent method
     *
     */
    protected function _setNumber()
    {
        if ($this->blFcFinalizeRedirect === false) {
            return parent::_setNumber();
        }
        $this->oxorder__oxordernr->value = $this->iFcOrderNr;
        return true;
    }

    /**
     * Not setting orderstatus for fatredirect when session flag is not set otherwise calls parent method
     *
     * @param $sStatus
     * @return void
     */
    protected function _setOrderStatus($sStatus)
    {
        if ($this->oxorder__oxpaymenttype->value == 'fatredirect' && $this->oxorder__oxtransstatus->value == "NOT_FINISHED" && $this->blFcFinalizeRedirect === false) {
            return;
        }
        parent::_setOrderStatus($sStatus);
    }

    /**
     * Loading order from sess_challenge if fatredirect session flag is set otherwise calls parent method
     *
     * @param Basket $oBasket
     * @return void
     */
    protected function _loadFromBasket(\OxidEsales\Eshop\Application\Model\Basket $oBasket)
    {
        if ($this->blFcFinalizeRedirect === false) {
            parent::_loadFromBasket($oBasket);
            return;
        }
        $this->load(Registry::getSession()->getVariable('sess_challenge'));
    }

    /**
     * Executing payment when fatredirect session flag is not set otherwise storing order number
     *
     * @param Basket $oBasket
     * @param $oUserpayment
     * @return bool|int
     */
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

    /**
     * Loading payment from order if fatredirect session flag is set otherwise calls parent method
     *
     * @param $sPaymentid
     * @return mixed|UserPayment|null
     */
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