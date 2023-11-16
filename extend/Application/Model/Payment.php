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
            ->where('oxid = ?')
            ->setParameter(0, 'fatpay');

        $oResult = $oQueryBuilder->execute();

        return !empty($oResult->fetchOne());
    }

    public function fcSetFatPayActive()
    {
        $this->load('fatpay');
        $this->oxpayments__oxactive = new \OxidEsales\Eshop\Core\Field(1);
        $this->save();
    }

    public function fcSetFatPayInActive()
    {
        $this->load('fatpay');
        $this->oxpayments__oxactive = new \OxidEsales\Eshop\Core\Field(0);
        $this->save();
    }

    public function fcCreateFatPayPayment()
    {
        $this->setId('fatpay');
        $this->oxpayments__oxdesc = new \OxidEsales\Eshop\Core\Field('FAT-Pay');
        $this->oxpayments__oxtoamount = new \OxidEsales\Eshop\Core\Field(1000000);
        $this->save();
    }
}