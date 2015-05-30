<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Content element configuration');

require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'content_element_ctypes.php');

$typoScript = '[GLOBAL] ';

foreach($contentElements as $element ) {

	//Get/Set environment
	$applicationEnv = getenv("APPLICATION_ENV");
	if($applicationEnv !== 'production') {
		$applicationEnv = 'stage';
	}

	//Load element ini
	$config = \Denkwerk\DwContentElements\Service\Ini::getInstance()
		->setConfigFile('typo3conf/ext/dw_content_elements_source/Configuration/Elements/'. ucfirst($element) .'.ini')
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

			//Set rendering typoScript
				$typoScript .= '
				tt_content.'.$element.' < tt_content.list.20.dwcontentelementssource_contentrenderer
				tt_content.'.$element.'.switchableControllerActions.Elements.1 = render';
		}

	}

}

//Add rendering typoScript
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript($_EXTKEY, 'setup', $typoScript, true);