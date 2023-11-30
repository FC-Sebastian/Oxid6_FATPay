<?php

namespace Fatchip\FATPay\Tests\Unit\extend\Core;

use \Fatchip\FATPay\extend\Core\ViewConfig;

class ViewConfigTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    public function testFcGetFatpayVersion()
    {
        require __DIR__.'/../../../../metadata.php';
        $oViewConfig = new ViewConfig();

        $this->assertEquals($aModule['version'], $oViewConfig->fcGetFatpayVersion());
    }
}