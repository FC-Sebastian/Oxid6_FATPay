<?php

namespace Fatchip\FATPay\extend\Application\Model;

use OxidEsales\Eshop\Core\Registry;
use Fatchip\FATPay\extend\Application\Model\ApiRequest;

class PaymentGateway extends PaymentGateway_parent
{
    /**
     * When payment is fatpay payment gets and evaluates api response otherwise returns parent::executePayment()
     *
     * @param $dAmount
     * @param $oOrder
     * @return bool
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function executePayment($dAmount, &$oOrder)
    {
        $blReturn = parent::executePayment($dAmount, $oOrder);

        if ($this->_oPaymentInfo->oxuserpayments__oxpaymentsid->value == 'fatpay' || $this->_oPaymentInfo->oxuserpayments__oxpaymentsid->value == 'fatredirect') {
            $oOrder->fcSetOrderNumber();
            $oRequest = oxNew(ApiRequest::class);
            $aResponse = $oRequest->getApiPostResponse($dAmount, $oOrder);

            if ($aResponse['status'] == 'APPROVED') {
                return true;
            } elseif ($aResponse['status'] == 'ERROR') {
                $this->_sLastError = Registry::getLang()->translateString($aResponse['errormessage']);
                return false;
            } elseif ($aResponse['status'] == 'REDIRECT') {
                $oOrder->oxorder__oxtransid = new \OxidEsales\Eshop\Core\Field($aResponse['transactionId']);
                $oOrder->save();
                Registry::getSession()->setVariable('fatRedirected', true);
                $this->fcRedirect($aResponse['redirectUrl'].'&transaction='.$aResponse['transactionId']);
            }
        }
        return $blReturn;
    }

    /**
     * Redirects to fatredirect validation
     *
     * @param $oOrder
     * @return void
     */
    public function fcRedirect($sRedirectUrl)
    {
        header('Location: '.$sRedirectUrl);
        exit();
    }
}