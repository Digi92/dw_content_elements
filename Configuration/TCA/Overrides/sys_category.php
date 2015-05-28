<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

//Set/Override tca table columns
$temporaryColumn = array(
	'sub_categories' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:dw_content_elements/Resources/Private/Language/locallang_db.xlf:tx_dwc_price',
        'config' => array(
            'type' => 'inline',
            'foreign_table' => 'sys_category',
            'foreign_field' => 'parent',
            'foreign_sortby' => 'sorting',
            'maxitems'      => 9999,
        ),
	),


);

//Add the tca columns to table
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
	'sys_category',
	$temporaryColumn
);
