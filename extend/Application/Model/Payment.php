<?php

namespace Fatchip\FATPay\extend\Application\Model;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;

class Payment extends Payment_parent
{
    public function fcHasFatPay()
    {
        $oQueryBuilder = ContainerFactory::getInstance()
            ->getContainer()
            ->get(QueryBuilderFactoryInterface::class)
            ->create();

        $oQueryBuilder
            ->select('oxid')
            ->from('oxpayments')
            ->where('oxid LIKE fatpay');

        $oResult = $oQueryBuilder->execute();

        return !empty($oResult->fetchOne());
    }
}