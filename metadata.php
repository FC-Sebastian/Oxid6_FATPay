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
    'thumbnail'   => '/out/src/img/fatpay.svg',
    'description' => [
        'de' => 'Ein Modul welches Zahlung mit FATPay ermöglicht',
        'en' => 'A module which enables paying with FATPay'
    ],
    'version'     => '1.0',
    'author'      => 'FC-Sebastian',
    'blocks'      => [
        [
            'template' => 'page/checkout/inc/payment_other.tpl',
            'block'    => 'checkout_payment_longdesc',
            'file'     => 'checkout_payment_longdesc.tpl'
        ],
        [
            'template' => 'page/checkout/order.tpl',
            'block'    => 'checkout_order_btn_submit_bottom',
            'file'     => 'checkout_order_btn_submit_bottom.tpl'
        ]
    ],
    'events'      => [
        'onActivate'   => 'Fatchip\FATPay\Core\ModuleEvents::onActivate',
        'onDeactivate' => 'Fatchip\FATPay\Core\ModuleEvents::onDeactivate'
    ],
    'extend'      => [
        \OxidEsales\Eshop\Application\Controller\OrderController::class => \Fatchip\FATPay\extend\Application\Controller\OrderController::class,
        \OxidEsales\Eshop\Application\Model\Order::class                => \Fatchip\FATPay\extend\Application\Model\Order::class,
        \OxidEsales\Eshop\Application\Model\PaymentGateway::class       => \Fatchip\FATPay\extend\Application\Model\PaymentGateway::class
    ],
    'settings'    => [
        ['group' => 'main', 'name' => 'fcfatpayApiUrl', 'type' => 'str', 'value' => ''],
        ['group' => 'main', 'name' => 'fcfatpayRedirectUrl', 'type' => 'str', 'value' => '']
    ]
];