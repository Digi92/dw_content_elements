<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

//Set/Override tca table columns
$temporaryColumn = array(
    'tx_dwc_headline' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:dw_content_elements_source/Resources/Private/Language/locallang_db.xlf:tx_dwc_headline',
        'config' => array(
            'type' => 'input',
            'size' => '30',
            'eval' => 'trim',
        ),
    ),
);

//Add the tca columns to table
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'tt_content',
    $temporaryColumn
);
