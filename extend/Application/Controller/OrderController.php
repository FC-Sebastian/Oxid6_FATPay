<?php

namespace Fatchip\FATPay\extend\Application\Controller;

use Fatchip\FATPay\extend\Application\Model\ApiRequest;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Core\Registry;

class OrderController extends OrderController_parent
{
    public function render()
    {
        $sSessChallenge = Registry::getSession()->getVariable('sess_challenge');
        $blFatRedirected = Registry::getSession()->getVariable('fatRedirected');
        if (!empty($sSessChallenge) && $blFatRedirected === true) {
            $this->fcCancelCurrentOrder();
        }
        Registry::getSession()->deleteVariable('fatRedirected');
        return parent::render();
    }

    public function fcFinalizeRedirect()
    {
        $sPaymentId = $this->getPayment()->getId();

        if ($sPaymentId === 'fatredirect') {
            Registry::getSession()->deleteVariable('fatRedirected');

            $oOrder = $this->fcGetCurrentOrder();
            if (!$oOrder) {
                $this->fcRedirectWithError('ORDER_NOT_FOUND');
            }

            $sTransactionId = $oOrder->oxorder__oxtransid->value;
            if (empty($sTransactionId)) {
                $this->fcCancelCurrentOrder();
                $this->fcRedirectWithError('TRANSACTION_ID_NOT_FOUND');
            }

            $oRequest = oxNew(ApiRequest::class);
            $aResponse = $oRequest->getApiGetResponse($sTransactionId);

            if ($aResponse = json_decode($aResponse, true)) {
                if ($aResponse['status'] === 'ERROR') {
                    $this->fcCancelCurrentOrder();
                    $this->fcRedirectWithError($aResponse['errormessage']);
                } else if ($aResponse['status'] === 'PENDING') {
                    $this->fcCancelCurrentOrder();
                    $this->fcRedirectWithError('TRANSACTION_PENDING');
                } else if ($aResponse['status'] === 'APPROVED') {
                    Registry::getSession()->setVariable('fatRedirectVerified', true);
                }
            }
        }

        $sReturn =  $this->execute();
        Registry::getSession()->deleteVariable('fatRedirectVerified');
        return $sReturn;
    }

    protected function fcGetCurrentOrder()
    {
        $sOrderId = Registry::getSession()->getVariable('sess_challenge');


        if (!empty($sOrderId)) {
            $oOrder = oxNew(Order::class);
            $oOrder->load($sOrderId);
            if ($oOrder->isLoaded()) {
                return $oOrder;
            }
        }
        return false;
    }

    protected function fcCancelCurrentOrder()
    {
        $sSessChallenge = Registry::getSession()->getVariable('sess_challenge');

        $oOrder = oxNew(Order::class);
        if ($oOrder->load($sSessChallenge) === true) {
            if ($oOrder->oxorder__oxtransstatus->value != 'OK') {
                $oOrder->cancelOrder();
            }
        }
        Registry::getSession()->deleteVariable('sess_challenge');
    }

    protected function fcRedirectWithError($sErrorLangIdent)
    {
        Registry::getSession()->setVariable('payerror', -50);
        Registry::getSession()->setVariable('payerrortext', Registry::getLang()->translateString($sErrorLangIdent));
        Registry::getUtils()->redirect(Registry::getConfig()->getCurrentShopUrl().'index.php?cl=payment');
    }
}