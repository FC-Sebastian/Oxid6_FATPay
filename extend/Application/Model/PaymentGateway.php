<?php

namespace Fatchip\FATPay\extend\Application\Model;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\ShopVersion;
use OxidEsales\Eshop\Core\ViewConfig;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface;

class PaymentGateway extends PaymentGateway_parent
{
    public function executePayment($dAmount, &$oOrder)
    {
        $blReturn = parent::executePayment($dAmount, $oOrder);

        if ($this->_oPaymentInfo->oxuserpayments__oxpaymentsid->value == 'fatpay' || $this->_oPaymentInfo->oxuserpayments__oxpaymentsid->value == 'fatredirect') {
            $aResponse = $this->fcGetApiResponse($dAmount, $oOrder);

            if ($aResponse['status'] == 'APPROVED') {
                return true;
            } elseif ($aResponse['status'] == 'ERROR') {
                $this->_sLastError = $aResponse['errormessage'];
                return false;
            } elseif ($aResponse['status'] == 'REDIRECT') {
                $oOrder->save();
                $this->fcRedirect($oOrder);
            }
        }
        return $blReturn;
    }

    public function fcRedirect($oOrder)
    {
        Registry::getUtils()->redirect(Registry::getConfig()->getConfigParam('fcfatpayRedirectUrl').'?orderId='.$oOrder->getId());
    }

    public function fcGetApiResponse($dAmount, $oOrder)
    {
        $sApiUrl = Registry::getConfig()->getConfigParam('fcfatpayApiUrl');
        $ch = curl_init($sApiUrl);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->fcGetFatPayParams($dAmount, $oOrder)));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $aResponse = curl_exec($ch);

        if (curl_errno($ch)) {
            Registry::getLogger()->error('FatPay curl error: '.curl_error($ch));
        }

        return json_decode($aResponse,true);
    }

    protected function fcGetFatPayParams($dAmount, $oOrder)
    {
        $oViewConf = oxNew(ViewConfig::class);
        $oUser = oxNew(User::class);
        $oUser->load($oOrder->oxorder__oxuserid->value);

        $aReturn['shopsystem'] = 'oxid';
        $aReturn['shopversion'] = ShopVersion::getVersion();
        $aReturn['moduleversion'] = $this->fcGetFatPayVerion();
        $aReturn['language'] = $oViewConf->getActLanguageAbbr();

        $aReturn['billing_firstname'] = $oOrder->oxorder__oxbillfname->value;
        $aReturn['billing_lastname'] = $oOrder->oxorder__oxbilllname->value;
        $aReturn['billing_street'] = $oOrder->oxorder__oxbillstreet->value;
        $aReturn['billing_zip'] = $oOrder->oxorder__oxbillzip->value;
        $aReturn['billing_city'] = $oOrder->oxorder__oxbillcity->value;
        $aReturn['billing_country'] = $oOrder->getBillCountry()->value;

        $aReturn['shipping_firstname'] = !empty($oOrder->oxorder__oxdelfname->value)
            ? $oOrder->oxorder__oxdelfname->value
            : $oOrder->oxorder__oxbillfname->value;
        $aReturn['shipping_lastname'] = !empty($oOrder->oxorder__oxdellname->value)
            ? $oOrder->oxorder__oxdellname->value
            : $oOrder->oxorder__oxbilllname->value;
        $aReturn['shipping_street'] = !empty($oOrder->oxorder__oxdelstreet->value)
            ? $oOrder->oxorder__oxdelstreet->value
            : $oOrder->oxorder__oxbillstreet->value;
        $aReturn['shipping_zip'] = !empty($oOrder->oxorder__oxdelzip->value)
            ? $oOrder->oxorder__oxdelzip->value
            : $oOrder->oxorder__oxbillzip->value;
        $aReturn['shipping_city'] = !empty($oOrder->oxorder__oxdelcity->value)
            ? $oOrder->oxorder__oxdelcity->value
            : $oOrder->oxorder__oxbillcity->value;
        $aReturn['shipping_country'] = !empty($oOrder->getDelCountry()->value)
            ? $oOrder->getDelCountry()->value
            : $oOrder->getBillCountry()->value;

        $aReturn['email'] = $oUser->oxuser__oxusername->value;
        $aReturn['customer_nr'] = $oUser->oxuser__oxcustnr->value;
        $aReturn['order_sum'] = $dAmount;
        $aReturn['currency'] = $oOrder->oxorder__oxcurrency->value;
        $aReturn['payment_type'] = $oOrder->oxorder__oxpaymenttype->value;

        return $aReturn;
    }

    protected function fcGetFatPayVerion()
    {
        return ContainerFactory::getInstance()
            ->getContainer()
            ->get(ShopConfigurationDaoBridgeInterface::class)
            ->get()->getModuleConfiguration('fcfatpay')->getVersion();
    }
}