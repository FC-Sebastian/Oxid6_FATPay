<?php

namespace Fatchip\FATPay\Api\Controller;

class FatRedirect extends ApiControllerBase
{
    protected $sView = 'fatredirect';
    protected $sTitle = 'FATRedirect Verification';
    protected $blStartsSession = true;

    public function render()
    {
        if ($this->getRequestParameter('fnc') === false) {
            $_SESSION['refererUrl'] = $_SERVER['HTTP_REFERER'];
        }
        parent::render();
    }

    /**
     * @throws \Exception
     */
    public function verify()
    {
        $iBirthday = strtotime($this->getRequestParameter('birthday'));

        if (time() - $iBirthday < 18 * 31536000) {
            throw new \Exception("You must be of age to use FatRedirect");
        }

        $this->setSessionParameter('FatRedirectVerified', true);

        $this->redirect($this->getSessionParameter('refererUrl').'&fnc=fcFinalizeRedirect');
    }
}