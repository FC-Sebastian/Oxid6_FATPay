<?php

namespace Fatchip\FATPay\Core;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;

class ModuleEvents
{
    protected static $aPayments = [
        ['id' => 'fatpay', 'desc' => 'FATPay', 'toAmount' => 1000000],
        ['id' => 'fatredirect', 'desc' => 'FATRedirect', 'toAmount' => 1000000],
    ];

    public static function onActivate()
    {
        self::insertFatPayPayment();
    }

    public static function onDeactivate()
    {
        self::setFatPayInactive();
    }

    /**
     * Inserts or activates fatpay payments
     *
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected static function insertFatPayPayment()
    {
        foreach (self::$aPayments as $aPayment) {
            if (self::hasFatPayPayment($aPayment['id']) === false) {
                self::createFatPayPayment($aPayment['id'], $aPayment['desc'], $aPayment['toAmount']);
            } else {
                self::setPaymentActive($aPayment['id'],1);
            }
        }
    }

    /**
     * Sets fatpay payments inactive
     *
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected static function setFatPayInactive()
    {
        foreach (self::$aPayments as $aPayment) {
            if (self::hasFatPayPayment($aPayment['id']) === true) {
                self::setPaymentActive($aPayment['id'],0);
            }
        }
    }

    /**
     * Returns true if payment found in db false otherwise
     *
     * @return bool
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected static function hasFatPayPayment($sPaymentId)
    {
        $oQueryBuilder = ContainerFactory::getInstance()
            ->getContainer()
            ->get(QueryBuilderFactoryInterface::class)
            ->create();

        $oQueryBuilder
            ->select('oxid')
            ->from('oxpayments')
            ->where('oxid = ?')
            ->setParameter(0, $sPaymentId);

        $oResult = $oQueryBuilder->execute();

        return !empty($oResult->fetchOne());
    }

    /**
     * Sets oxactive column in oxpayments to $iValue
     *
     * @param $sPaymentId
     * @param $iValue
     * @return void
     * @throws \Exception
     */
    protected static function setPaymentActive($sPaymentId, $iValue)
    {
        $oPayment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);
        $oPayment->load($sPaymentId);
        $oPayment->assign(['oxactive' => $iValue]);
        $oPayment->save();
    }

    /**
     * Stores payment in db
     *
     * @param $sId
     * @param $sDesc
     * @param $iToAmount
     * @return void
     * @throws \Exception
     */
    protected static function createFatPayPayment($sId, $sDesc, $iToAmount)
    {
        $oPayment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);
        $oPayment->setId($sId);
        $oPayment->assign(['oxtoamount' => $iToAmount]);
        $oPayment->save();

        self::setDescription($sId, $sDesc);
        self::setDelivery($sId);
    }

    /**
     * Sets payment description
     *
     * @param $sOxid
     * @param $sDesc
     * @return void
     * @throws \Exception
     */
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

    /**
     * Assigns delivery options to payment
     *
     * @param $sOxid
     * @return void
     * @throws \Exception
     */
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

    /**
     * Returns array of delivery options
     *
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
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