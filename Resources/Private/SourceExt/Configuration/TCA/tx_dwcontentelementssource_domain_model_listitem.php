<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

if (!defined('TYPO3')) {
    die('Access denied.');
}

$ll = 'LLL:EXT:dw_content_elements_source/Resources/Private/Language/locallang_db.xlf:';

//Set/Override tca table columns
$temporaryColumn = array(
    'tx_dwcontentelementssource_domain_model_listitem' => array(
        'exclude' => 0,
        'label' => $ll . 'tx_dwcontentelementssource_domain_model_listitem',
        'config' => array(
            'type' => 'inline',
            'foreign_table' => 'tx_dwcontentelementssource_domain_model_listitem',
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
    ),
);

//Add the tca columns to table
ExtensionManagementUtility::addTCAcolumns(
    'tt_content',
    $temporaryColumn
);

//Set/Override tca table columns
return [
    'ctrl' => array(
        'title' => $ll . 'tx_dwcontentelementssource_domain_model_listitem',
        'label' => 'headline',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'hideTable' => true,
        'sortby' => 'sorting',
        'versioningWS' => true,
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
        'iconfile' => 'EXT:dw_content_elements_source/Resources/Public/Icons/IRRE.gif'
    ),
    'types' => array(
        '1' => array('showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, headline, text,
		--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access,starttime, endtime'),
    ),
    'palettes' => array(
        '1' => array('showitem' => ''),
    ),
    'columns' => array(
        'sys_language_uid' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => ['type' => 'language']
        ),
        'l10n_parent' => array(
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => array(
                    array('', 0),
                ),
                'foreign_table' => 'tx_dwcontentelementssource_domain_model_listitem',
                'foreign_table_where' => 'AND tx_dwcontentelementssource_domain_model_listitem.pid=###CURRENT_PID### 
                    AND tx_dwcontentelementssource_domain_model_listitem.sys_language_uid IN (-1,0)',
                'default' => 0,
            ),
        ),
        'l10n_diffsource' => array(
            'config' => array(
                'type' => 'passthrough',
            ),
        ),
        't3ver_label' => array(
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            )
        ),
        'hidden' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => array(
                'type' => 'check',
            ),
        ),
        'starttime' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => array(
                'type' => 'input',
                'size' => 13,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => array(
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ),
                'renderType' => 'inputDateTime',
                ['behaviour' => ['allowLanguageSynchronization' => true]],
            ),
        ),
        'endtime' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => array(
                'type' => 'input',
                'size' => 13,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => array(
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ),
                'renderType' => 'inputDateTime',
                ['behaviour' => ['allowLanguageSynchronization' => true]],
            ),
        ),
        'headline' => array(
            'exclude' => 1,
            'label' => $ll . 'tx_dwcontentelementssource_domain_model_listitem.headline',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'required' => true
            ),
        ),
        'text' => array(
            'exclude' => 1,
            'label' => $ll . 'tx_dwcontentelementssource_domain_model_listitem.text',
            'config' => array(
                'type' => 'text',
                'cols' => '40',
                'rows' => '15',
                'eval' => 'trim',
                'enableRichtext' => true,
                'richtextConfiguration' => 'default'
            )
        ),
    ),
];
