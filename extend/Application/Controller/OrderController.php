<?php

namespace Fatchip\FATPay\extend\Application\Controller;

use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Core\Registry;

class OrderController extends OrderController_parent
{

    public function execute()
    {
        Registry::getLogger()->error(json_encode($_SESSION));
        return parent::execute();
    }

    /**
     * Redirects to thankyou controller
     *
     * @return void
     */
    public function fcRedirect()
    {
        Registry::getUtils()->redirect($this->getConfig()->getCurrentShopUrl() . '?cl=thankyou');
    }
}