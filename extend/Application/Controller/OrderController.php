<?php

namespace Fatchip\FATPay\extend\Application\Controller;

use Fatchip\FATPay\Application\Model\ApiRequest;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Core\Registry;
use Fatchip\FATPay\Application\Model\FatPayHelper;

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

    /**
     * Finishes fatredirect payment
     *
     * @return string|null
     */
    public function fcFinalizeRedirect()
    {
        $sPaymentId = $this->getPayment()->getId();

        if ($this->isFatRedirect($sPaymentId)) {
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

            $aResponse = $this->getApiResponse($sTransactionId);
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
            } else {
                $this->fcCancelCurrentOrder();
                $this->fcRedirectWithError('COULDNT_CONNECT_TO_API');
            }
        } else {
            $this->fcCancelCurrentOrder();
            $this->fcRedirectWithError('INVALID_PAYMENTTYPE');
        }

        $sReturn =  $this->execute();
        Registry::getSession()->deleteVariable('fatRedirectVerified');
        return $sReturn;
    }

    public function getApiResponse($sTransactionId)
    {
        $oRequest = oxNew(ApiRequest::class);
        return $oRequest->getApiGetResponse($sTransactionId);
    }

    /**
     * Loads order via session and returns it
     *
     * @return false|mixed|Order
     */
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

    /**
     * Cancels order via session and deletes sess_challange
     *
     * @return void
     */
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

    /**
     * Redirects to payment page with error
     *
     * @param $sErrorLangIdent
     * @return void
     */
    protected function fcRedirectWithError($sErrorLangIdent)
    {
        Registry::getSession()->setVariable('payerror', -69);
        Registry::getSession()->setVariable('payerrortext', Registry::getLang()->translateString($sErrorLangIdent));
        Registry::getUtils()->redirect(Registry::getConfig()->getCurrentShopUrl().'index.php?cl=payment');
    }
}