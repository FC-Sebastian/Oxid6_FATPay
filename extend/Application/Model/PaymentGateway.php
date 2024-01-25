<?php

namespace Fatchip\FATPay\extend\Application\Model;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\ViewConfig;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface;

class PaymentGateway extends PaymentGateway_parent
{
    public function executePayment($dAmount, &$oOrder)
    {
        $blReturn = parent::executePayment($dAmount, $oOrder);

        if ($this->_oPaymentInfo->oxuserpayments__oxpaymentsid->value == 'fatpay') {
            $sApiUrl = Registry::getConfig()->getConfigParam('fcfatpayApiUrl');
            $ch = curl_init($sApiUrl);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->fcGetFatPayParams($dAmount, $oOrder));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

            $sResponse = curl_exec($ch);

            if (curl_errno($ch)) {
                Registry::getLogger()->error('FatPay curl error: '.curl_error($ch));
            }

            $sResponse = json_decode($sResponse,true);
            if ($sResponse['status'] == 'APPROVED') {
                return true;
            } elseif ($sResponse['status'] == 'ERROR') {
                $this->_sLastError = $sResponse['errormessage'];
                return false;
            }
        }
        return $blReturn;
    }

    protected function fcGetFatPayParams($dAmount, $oOrder)
    {
        $oViewConf = oxNew(ViewConfig::class);
        $oUser = oxNew(User::class);
        $oUser->load($oOrder->oxorder__oxuserid->value);

        $aReturn['shopsystem'] = 'oxid';
        $aReturn['shopversion'] = $oViewConf->getShopVersion();
        $aReturn['moduleversion'] = ContainerFactory::getInstance()
            ->getContainer()
            ->get(ShopConfigurationDaoBridgeInterface::class)
            ->get()->getModuleConfiguration('fcfatpay')->getVersion();
        $aReturn['language'] = $oViewConf->getActLanguageAbbr();

        $aReturn['billing_firstname'] = $oOrder->oxorder__oxbillfname->value;
        $aReturn['billing_lastname'] = $oOrder->oxorder__oxbilllname->value;
        $aReturn['billing_street'] = $oOrder->oxorder__oxbillstreet->value;
        $aReturn['billing_zip'] = $oOrder->oxorder__oxbillzip->value;
        $aReturn['billing_city'] = $oOrder->oxorder__oxbillcity->value;
        $aReturn['billing_country'] = $oOrder->getBillCountry();

        $aReturn['shipping_firstname'] = !empty($oOrder->oxorder__oxdelfname->value) ? $oOrder->oxorder__oxdelfname->value : $oOrder->oxorder__oxbillfname->value;
        $aReturn['shipping_lastname'] = !empty($oOrder->oxorder__oxdellname->value) ? $oOrder->oxorder__oxdellname->value : $oOrder->oxorder__oxbilllname->value;
        $aReturn['shipping_street'] = !empty($oOrder->oxorder__oxdelstreet->value) ? $oOrder->oxorder__oxdelstreet->value : $oOrder->oxorder__oxbillstreet->value;
        $aReturn['shipping_zip'] = !empty($oOrder->oxorder__oxdelzip->value) ? $oOrder->oxorder__oxdelzip->value : $oOrder->oxorder__oxbillzip->value;
        $aReturn['shipping_city'] = !empty($oOrder->oxorder__oxdelcity->value) ? $oOrder->oxorder__oxdelcity->value : $oOrder->oxorder__oxbillcity->value;
        $aReturn['shipping_country'] = !empty($oOrder->getDelCountry()) ? $oOrder->getDelCountry() : $oOrder->getBillCountry();

        $aReturn['email'] = $oUser->oxuser__oxusername->value;
        $aReturn['customer_nr'] = $oUser->oxuser__oxcustnr->value;
        $aReturn['order_sum'] = $dAmount;
        $aReturn['currency'] = $oOrder->oxorder__oxcurrency->value;
        $aReturn['payment_type'] = $oOrder->oxorder__oxpaymentid->value;

        return $aReturn;
    }
}