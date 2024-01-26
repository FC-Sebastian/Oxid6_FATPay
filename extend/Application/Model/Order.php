<?php

namespace Fatchip\FATPay\extend\Application\Model;

class Order extends Order_parent
{
    public function fcSetOrderNumber()
    {
        if (!$this->oxorder__oxordernr->value) {
            $this->_setNumber();
        }
    }
}