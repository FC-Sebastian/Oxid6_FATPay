<?php

namespace Fatchip\FATPay\extend\Core;

class ViewConfig extends ViewConfig_parent
{
    public function fatpayGetModuleVersion()
    {
        $oContainer = \OxidEsales\EshopCommunity\Internal\Container\ContainerFactory::getInstance()->getContainer();
        $oContainer = $oContainer->get(\OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface::class)->get();
        return json_encode(get_class_methods($oContainer->getModuleConfiguration('fcfatpay')));
    }
}