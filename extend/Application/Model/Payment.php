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
        $this->assign(['oxactive' => 1]);
        $this->save();
    }

    public function fcSetFatPayInActive()
    {
        $this->load('fatpay');
        $this->assign(['oxactive' => 0]);
        $this->save();
    }

    public function fcCreateFatPayPayment()
    {
        $this->setId('fatpay');
        $this->assign(['oxtoamount' => 1000000]);
        $this->save();

        $this->fcSetDescription();
        $this->fcSetDelivery();
    }

    protected function fcSetDescription()
    {
        $oLang = \OxidEsales\Eshop\Core\Registry::getLang();
        foreach ($oLang->getLanguageArray() as $aLang) {
            $this->loadInLang($aLang->id, 'fatpay');
            $this->assign(['oxdesc' => 'FATPay']);
            $this->save();
        }
    }

    protected function fcSetDelivery()
    {
        $aDeliveryOptions = $this->fcGetDeliveryOptions();
        if (!empty($aDeliveryOptions)) {
            foreach ($aDeliveryOptions as $aDeliveryOption) {
                $oModel = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
                $oModel->init('oxobject2payment');
                $oModel->assign(
                    [
                        'oxpaymentid' => 'fatpay',
                        'oxobjectid'  => $aDeliveryOption['oxid'],
                        'oxtype' => 'oxdelset'
                    ]
                );
                $oModel->save();
            }
        }
    }

    protected function fcGetDeliveryOptions()
    {
        $oQueryBuilder = ContainerFactory::getInstance()
            ->getContainer()
            ->get(QueryBuilderFactoryInterface::class)
            ->create();

        $oResult = $oQueryBuilder
            ->select('oxid')
            ->from('oxdeliveryset')
            ->execute();

        return $oResult->fetchAllAssociative();
    }
}