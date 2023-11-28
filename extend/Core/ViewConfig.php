<?php

namespace Fatchip\FATPay\extend\Core;

class ViewConfig extends ViewConfig_parent
{
    public function fatpayGetModuleVersion()
    {
        $oContainer = \OxidEsales\EshopCommunity\Internal\Container\ContainerFactory::getInstance()->getContainer();
        $oContainer = $oContainer->get(\OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface::class);
        return var_export($oContainer,true);
    }
}