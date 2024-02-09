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
                Registry::getLogger()->error('ORDERCONTROLLER ORDER NOT FOUND');
            }

            $sTransactionId = $oOrder->oxorder__oxtransid->value;
            if (empty($sTransactionId)) {
                Registry::getLogger()->error('ORDERCONTROLELR TRANSACTION ID EMPTY');
            }

            $oRequest = oxNew(ApiRequest::class);
            $aResponse = $oRequest->getApiGetResponse($sTransactionId);

            if ($aResponse = json_decode($aResponse, true)) {
                if ($aResponse['status'] === 'APPROVED') {
                    Registry::getSession()->setVariable('fatRedirectVerified', true);
                }
            }
        }

        $sReturn =  $this->execute();
        Registry::getSession()->deleteVariable('fatRedirected');
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
}