<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Content element configuration');

//Palettes
$TCA['tt_content']['palettes']['headerText'] = array();
$TCA['tt_content']['palettes']['headerText']['showitem'] ='header, --linebreak--, subheader';
$TCA['tt_content']['palettes']['headerText']['canNotCollapse']='1';

require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'content_element_ctypes.php');

foreach($contentElements as $element ) {

	//Get/Set environment
	$applicationEnv = getenv("APPLICATION_ENV");
	if($applicationEnv !== 'production') {
		$applicationEnv = 'stage';
	}

	//Load element ini
	$config = \Denkwerk\DwContentElements\Service\Ini::getInstance()
		->setConfigFile('typo3conf/ext/dw_content_elements/Configuration/Elements/'. ucfirst($element) .'.ini')
		->setEnviroment($applicationEnv)
		->loadConfig();

	if(empty($config) === FALSE) {

		if((bool)$config->title) {

			//Add element plugin
			\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(array($config->title, $element), 'CType');

			//Set element showitem
			if((bool)$config->overWriteShowitem === TRUE) {
				$showItem = trim((string)$config->fields, ',');
			} else {
				$showItem = 'CType;;4;button;1-1-1, --palette--;Headline,' . trim((string)$config->fields, ',') . ',
			--div--;LLL:EXT:cms/locallang_tca.xml:pages.tabs.access,starttime, endtime, fe_group';
			}
			$TCA['tt_content']['types'][$element]['showitem'] = $showItem;
            $TCA['tt_content']['types'][$element]['tx_dw_content_elements_title'] = $config->title;
		}

	}

}


//=============================================== Einbindung Irrel BEGIN ===============================================//

//----------------------------------------------- tx_dwc_related_link_item -----------------------------------------------//
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_dwc_related_link_item');

$TCA['tx_dwc_related_link_item'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:dw_content_elements/Resources/Private/Language/locallang_db.xlf:tx_dwc_related_link_item',
		'label' => 'link_text',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'hideTable' => TRUE,
		'sortby' => 'sorting',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
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
		'searchFields' => 'link_text',
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/IRRE/RelatedLink.php',
	),
);

$GLOBALS['TCA']['tt_content']['columns']['tx_dwc_related_link_item'] = array(
	'exclude' => 0,
	'label' => 'LLL:EXT:dw_content_elements/Resources/Private/Language/locallang_db.xlf:tx_dwc_related_link_item',
	'config' => array(
		'type' => 'inline',
		'foreign_table' => 'tx_dwc_related_link_item',
		'foreign_field' => 'foreign_uid',
		'maxitems'      => 9999,
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

