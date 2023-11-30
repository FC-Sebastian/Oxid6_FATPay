<?php

namespace Fatchip\FATPay\extend\Core;

class ViewConfig extends ViewConfig_parent
{
    public function fcGetFatpayVersion()
    {
        $oContainer = \OxidEsales\EshopCommunity\Internal\Container\ContainerFactory::getInstance()->getContainer();
        $oContainer = $oContainer->get(\OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface::class)->get();
        return $oContainer->getModuleConfiguration('fcfatpay')->getVersion();
    }
}