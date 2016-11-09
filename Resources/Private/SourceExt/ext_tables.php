<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $_EXTKEY,
    'Configuration/TypoScript',
    'Content element configuration'
);

//Palettes
$TCA['tt_content']['palettes']['headerText'] = array();
$TCA['tt_content']['palettes']['headerText']['showitem'] = 'tx_dwc_headline, --linebreak--, subheader';
$TCA['tt_content']['palettes']['headerText']['canNotCollapse'] = '1';


//============================================== Einbindung Irrel BEGIN ==============================================//

//----------------------------------------------- tx_dwc_list_item -----------------------------------------------//
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_dwc_list_item');

$TCA['tx_dwc_list_item'] = array(
    'ctrl' => array(
        'title' => 'LLL:EXT:dw_content_elements_source/Resources/Private/Language/locallang_db.xlf:tx_dwc_list_item',
        'label' => 'headline',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'hideTable' => true,
        'sortby' => 'sorting',
        'versioningWS' => 2,
        'versioning_followPages' => true,
        'origUid' => 't3_origuid',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => array(
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ),
        'searchFields' => 'headline, text',
        'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) .
            'Configuration/TCA/IRRE/List.php',
        'iconfile' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/IRRE.gif'
    ),
);

$GLOBALS['TCA']['tt_content']['columns']['tx_dwc_list_item'] = array(
    'exclude' => 0,
    'label' => 'LLL:EXT:dw_content_elements_source/Resources/Private/Language/locallang_db.xlf:tx_dwc_list_item',
    'config' => array(
        'type' => 'inline',
        'foreign_table' => 'tx_dwc_list_item',
        'foreign_field' => 'foreign_uid',
        'maxitems' => 9999,
        'appearance' => array(
            'collapseAll' => 1,
            'levelLinksPosition' => 'top',
            'showSynchronizationLink' => 1,
            'showPossibleLocalizationRecords' => 1,
            'showAllLocalizationLink' => 1
        ),
    ),
);

//=============================================== Einbindung Irrel END ===============================================//
