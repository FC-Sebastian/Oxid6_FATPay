<?php
include __DIR__."/autoloader.php";
$controllerNameSpace = 'Fatchip\FATPay\Api\Controller\\';

$controller = "FatRedirect";
if (isset($_REQUEST['cl'])) {
    $controller = $_REQUEST['cl'];
}

$controller = $controllerNameSpace.$controller;

$controllerObject = new $controller();
if (isset($_REQUEST['fnc'])) {
    $action = strtolower($_REQUEST['fnc']);
    if (method_exists($controllerObject, $action)) {
        try {
            $controllerObject->$action();
        } catch (\Throwable $exc) {
            $controllerObject->setErrorMessage($exc->getMessage());
        }

    }
}

$controllerObject->render();