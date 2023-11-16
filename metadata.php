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
            'template' => '',
            'block'    => '',
            'file'     => ''
        ],
    ],
    'events'      => [
        'onActivate' => '\Fatchip\FATPay\Core\ModuleEvents::onActivate'
    ],
    'extend'      => [
    ],
    'controllers' => [
    ],
    'templates'   => [
    ]
];