<?php

namespace Fatchip\FATPay\Api\Controller;

class ApiControllerBase
{
    protected $blStartsSession = false;
    protected $sView = false;
    protected $sTitle = false;
    protected $sError = false;

    public function __construct()
    {
        if ($this->blStartsSession === true) {
            session_start();
        }
    }

    public function getSessionParameter($sKey)
    {
        if (isset($_SESSION[$sKey])) {
            return $_SESSION[$sKey];
        }
        return null;
    }

    public function setSessionParameter($sKey, $sValue)
    {
        $_SESSION[$sKey] = $sValue;
    }

    public function setErrorMessage($error)
    {
        $this->sError = $error;
    }

    public function getError()
    {
        return $this->sError;
    }

    public function getSTitle()
    {
        return $this->sTitle;
    }

    public function getRequestParameter($key, $default = false)
    {
        if (isset($_REQUEST[$key])) {
            return $_REQUEST[$key];
        }
        return $default;
    }

    /**
     * @throws \Exception
     */
    public function render()
    {
        if ($this->sView === false) {
            throw new \Exception("NO VIEW FOUND");
        }

        $viewPath = __DIR__ . "/../Views/" . $this->sView . ".php";
        if (!file_exists($viewPath)) {
            throw new \Exception("VIEW FILE NOT FOUND");
        }

        $controller = $this;

        ob_start();
        try {
            $title = $controller->getSTitle();
            include $viewPath;
        } catch (\Throwable $exc) {
            $controller->setErrorMessage($exc->getMessage());
        }
        $output = ob_get_contents();
        ob_end_clean();

        include __DIR__ . "/../Views/header.php";
        echo $output;
        include __DIR__ . "/../Views/footer.php";
    }

    protected function redirect($url)
    {
        header("location: " . $url);
        exit();
    }
}