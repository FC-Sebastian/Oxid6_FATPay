<?php

namespace Fatchip\FATPay\Core\ModuleEvents;

class ModuleEvents
{
    public static function onActivate()
    {
        self::placeHolder();
    }

    public static function placeHolder()
    {
        \OxidEsales\Eshop\Core\Registry::getLogger()->error('AOOOOGA');
    }
}