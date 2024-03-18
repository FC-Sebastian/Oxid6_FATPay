<?php

namespace Fatchip\FATPay\Application\Model;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\ShopVersion;
use OxidEsales\Eshop\Core\ViewConfig;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface;
use Fatchip\FATPay\Application\Model\CurlHelper;

class ApiRequest
{
    protected $aFatPayUrlCopyParameters = [
        'stoken',
        'sDeliveryAddressMD5',
        'oxdownloadableproductsagreement',
        'oxserviceproductsagreement',
    ];
    protected $ch = null;

    /**
     * Sends order data to api and returns response
     *
     * @param $dAmount
     * @param $oOrder
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getApiPostResponse($dAmount, $oOrder)
    {
        $oCurlHelper = $this->getCurlHelper();
        return $oCurlHelper->executeApiPostRequest(Registry::getConfig()->getConfigParam('fcfatpayApiUrl'), $this->getFatPayParams($dAmount, $oOrder));;
    }

    /**
     * Gets transaction status from api via transaction id
     *
     * @param $sTransactionId
     * @return bool|string|string[]
     */
    public function getApiGetResponse($sTransactionId)
    {
        $oCurlHelper = $this->getCurlHelper();
        $sApiUrl =  Registry::getConfig()->getConfigParam('fcfatpayApiUrl').'?transaction='.$sTransactionId;

        return $oCurlHelper->executeApiGetRequest($sApiUrl);
    }

    public function getCurlHelper()
    {
        return new CurlHelper();
    }

    /**
     * Returns return url for fatredirect
     *
     * @return string
     */
    public function fcGetReturnUrl()
    {
        $sBaseUrl = Registry::getConfig()->getCurrentShopUrl().'index.php?cl=order&fnc=fcFinalizeRedirect';

        return $sBaseUrl.$this->getAdditionalUrlParameters();
    }

    /**
     * Returns additional url parameters for checkout
     *
     * @return string
     */
    public function getAdditionalUrlParameters()
    {
        $oRequest = Registry::getRequest();
        $oSession = Registry::getSession();

        $sAddParams = '';

        foreach ($this->aFatPayUrlCopyParameters as $sParamName) {
            $sValue = $oRequest->getRequestEscapedParameter($sParamName);
            if (!empty($sValue)) {
                $sAddParams .= '&'.$sParamName.'='.$sValue;
            }
        }

        $sSid = $oSession->sid(true);
        if ($sSid != '') {
            $sAddParams .= '&'.$sSid;
        }

        if (!$oRequest->getRequestEscapedParameter('stoken')) {
            $sAddParams .= '&stoken='.$oSession->getSessionChallengeToken();
        }
        $sAddParams .= '&ord_agb=1';
        $sAddParams .= '&rtoken='.$oSession->getRemoteAccessToken();

        return $sAddParams;
    }

    /**
     * Returns order data as array
     *
     * @param $dAmount
     * @param $oOrder
     * @return false|string
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function getFatPayParams($dAmount, $oOrder)
    {
        $oViewConf = oxNew(ViewConfig::class);
        $oUser = oxNew(User::class);
        $oUser->load($oOrder->oxorder__oxuserid->value);

        $aReturn['shopsystem'] = 'oxid';
        $aReturn['shopversion'] = ShopVersion::getVersion();
        $aReturn['moduleversion'] = $this->getFatPayVerion();
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
        $aReturn['order_nr'] = $oOrder->oxorder__oxordernr->value;
        $aReturn['order_sum'] = $dAmount;
        $aReturn['currency'] = $oOrder->oxorder__oxcurrency->value;
        $aReturn['payment_type'] = $oOrder->oxorder__oxpaymenttype->value;
        $aReturn['redirectUrl'] = $this->fcGetReturnUrl();

        return json_encode($aReturn);
    }

    /**
     * Returns version of fatpay module
     *
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function getFatPayVerion()
    {
        return ContainerFactory::getInstance()
            ->getContainer()
            ->get(ShopConfigurationDaoBridgeInterface::class)
            ->get()->getModuleConfiguration('fcfatpay')->getVersion();
    }
}