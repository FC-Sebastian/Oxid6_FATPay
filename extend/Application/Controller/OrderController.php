<?php

namespace Fatchip\FATPay\extend\Application\Controller;

use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Core\Registry;

class OrderController extends OrderController_parent
{
    public function fcFinalizeRedirect()
    {
        $this->getSession()->setVariable('fatRedirectVerified', true);
        return $this->execute();
    }
}