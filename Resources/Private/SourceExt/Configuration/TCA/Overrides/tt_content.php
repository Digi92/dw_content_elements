<?php
if (!defined('TYPO3')) {
    die('Access denied.');
}

//Palettes
$GLOBALS['TCA']['tt_content']['palettes']['headerText'] = array();
$GLOBALS['TCA']['tt_content']['palettes']['headerText']['showitem'] = 'tx_dwc_headline, --linebreak--, subheader';
$GLOBALS['TCA']['tt_content']['palettes']['headerText']['canNotCollapse'] = '1';

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
