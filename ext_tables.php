<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// Add TypoScript static files
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $_EXTKEY,
    'Configuration/TypoScript',
    'Content Element configuration'
);

//Add backend module
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
    'Denkwerk.' . $_EXTKEY,   // vendor + extkey, seperated by a dot
    'tools',                  // Backend Module group to place the module in
    'DW Content Elements',    // module name
    '',                       // position in the group
    array(                    // Allowed controller -> action combinations
        'Backend' => 'index, createSourceExt, loadSourceExt',
    ),
    array(                    // Additional configuration
        'access' => 'user,group',
        'icon' => 'EXT:' . $_EXTKEY . '/ext_icon.png',
        'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xlf',
    )
);
