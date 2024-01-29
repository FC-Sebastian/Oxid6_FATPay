<?php

namespace Fatchip\FATPay\extend\Application\Model;

class Order extends Order_parent
{
    /**
     * Sets order number
     *
     * @return void
     */
    public function fcSetOrderNumber()
    {
        if (!$this->oxorder__oxordernr->value) {
            $this->_setNumber();
        }
    }
}