<?php

namespace Fatchip\FATPay\Api\Controller;

class FatRedirect extends ApiControllerBase
{
    protected $sView = 'fatredirect';
    protected $sTitle = 'FATRedirect Verification';
    protected $blStartsSession = true;

    /**
     * @throws \Exception
     */
    public function verify()
    {
        $iBirthday = strtotime($this->getRequestParameter('refererUrl'));
        $iOfAge = strtotime('+18 years', $iBirthday);

        if (time() < $iOfAge) {
            throw new \Exception("You must be of age to use FatRedirect");
        }

        $this->setSessionParameter('FatRedirectVerified', true);

        $this->redirect($this->getSessionParameter('oxidUrl'));
    }
}