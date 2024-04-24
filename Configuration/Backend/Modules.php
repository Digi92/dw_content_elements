<?php
declare(strict_types=1);

return [
    'dw_content_elements_backend_module' => [
        'parent' => 'system',
        'access' => 'user',
        'workspaces' => 'live',
        'path' => '/module/dwcontentelements',
        'labels' => 'LLL:EXT:dw_content_elements/Resources/Private/Language/locallang_mod.xlf',
        'iconIdentifier' => 'dw-content-element-module-icon',
        'extensionName' => 'DwContentElements',
        'controllerActions' => [
            \Denkwerk\DwContentElements\Controller\BackendController::class => [
                'index', 'createSourceExt', 'loadSourceExt',
            ],
        ],
    ],
];
