<?php

/**
 * Metadata version
 */
$sMetadataVersion = '2.1';

/**
 * Module information
 */
$aModule = [
    'id'          => 'fcfatpay',
    'title'       => [
        'de' => 'FATPay-Zahlungsmodul',
        'en' => 'FATPay payment module'
    ],
    'description' => [
        'de' => 'Ein Modul welches Zahlung mit FATPay ermöglicht',
        'en' => 'A module which enables paying with FATPay'
    ],
    'version'     => '1.0',
    'author'      => 'FC-Sebastian',
    'blocks'      => [
        [
            'template' => 'page/chechout/payment_other.tpl',
            'block'    => 'select_payment',
            'file'     => 'select_payment.tpl'
        ],
    ],
    'events'      => [
        'onActivate' => 'Fatchip\FATPay\Core\ModuleEvents::onActivate',
        'onDeactivate' => 'Fatchip\FATPay\Core\ModuleEvents::onDeactivate'
    ],
    'extend'      => [
        \OxidEsales\Eshop\Application\Model\Payment::class => \Fatchip\FATPay\extend\Application\Model\Payment::class
    ],
    'controllers' => [
    ],
    'templates'   => [
    ],
    'settings'    => [
        ['group' => 'main', 'name' => 'sFcApiUrl', 'type' => 'str', 'value' => '']
    ]
];