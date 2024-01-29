<?php

namespace Fatchip\FATPay\Core;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;

class ModuleEvents
{
    protected static $aPayments = [
        ['id' => 'fatpay', 'desc' => 'FATPay', 'toAmount' => 1000000],
        ['id' => 'fatpay', 'desc' => 'FATPay', 'toAmount' => 1000000],
    ];

    public static function onActivate()
    {
        self::insertFatPayPayment();
    }

    public static function onDeactivate()
    {
        self::setFatPayInactive();
    }

    protected static function insertFatPayPayment()
    {
        if (self::hasFatPay() === false) {
            foreach (self::$aPayments as $aPayment) {
                self::createFatPayPayment($aPayment['id'], $aPayment['desc'], $aPayment['toAmount']);
            }
        } else {
            foreach (self::$aPayments as $aPayment) {
                self::setPaymentActive($aPayment['id'],1);
            }
        }
    }

    protected static function setFatPayInactive()
    {
        if (self::hasFatPay() === true) {
            foreach (self::$aPayments as $aPayment) {
                self::setPaymentActive($aPayment['id'],0);
            }
        }
    }

    protected static function hasFatPay()
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

    protected static function setPaymentActive($sPaymentId, $iValue)
    {
        $oPayment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);
        $oPayment->load($sPaymentId);
        $oPayment->assign(['oxactive' => $iValue]);
        $oPayment->save();
    }

    protected static function createFatPayPayment($sId, $sDesc, $iToAmount)
    {
        $oPayment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);
        $oPayment->setId($sId);
        $oPayment->assign(['oxtoamount' => $iToAmount]);
        $oPayment->save();

        self::setDescription($sId, $sDesc);
        self::setDelivery($sId);
    }

    protected static function setDescription($sOxid, $sDesc)
    {
        $oLang = \OxidEsales\Eshop\Core\Registry::getLang();
        $oPayment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);
        foreach ($oLang->getLanguageArray() as $aLang) {
            $oPayment->loadInLang($aLang->id, $sOxid);
            $oPayment->assign(['oxdesc' => $sDesc]);
            $oPayment->save();
        }
    }

    protected static function setDelivery($sOxid)
    {
        $aDeliveryOptions = self::getDeliveryOptions();
        if (!empty($aDeliveryOptions)) {
            foreach ($aDeliveryOptions as $aDeliveryOption) {
                $oModel = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
                $oModel->init('oxobject2payment');
                $oModel->assign(
                    [
                        'oxpaymentid' => $sOxid,
                        'oxobjectid'  => $aDeliveryOption['oxid'],
                        'oxtype' => 'oxdelset'
                    ]
                );
                $oModel->save();
            }
        }
    }

    protected static function getDeliveryOptions()
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