<?php
if (!defined('TYPO3')) {
    die('Access denied.');
}

//Add backend module
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
    'DwContentElements',   // vendor + extkey, seperated by a dot
    'tools',                  // Backend Module group to place the module in
    'DW Content Elements',    // module name
    '',                       // position in the group
    array(                    // Allowed controller -> action combinations
        \Denkwerk\DwContentElements\Controller\BackendController::class => 'index, createSourceExt, loadSourceExt',
    ),
    array(                    // Additional configuration
        'access' => 'user,group',
        'icon' => 'EXT:dw_content_elements/ext_icon.png',
        'labels' => 'LLL:EXT:dw_content_elements/Resources/Private/Language/locallang_db.xlf',
    )
);
