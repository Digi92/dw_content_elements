<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Content element configuration');

//Source Extension Installiert
if(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('dw_content_elements_source')) {

	$typoScript = '[GLOBAL] ';

	// Extension manager configuration
	$configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['dw_content_elements']);

	//Add content element wizard tab
	if ((bool)$configuration['addElementsToWizard'] === TRUE) {
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
			mod.wizards.newContentElement.wizardItems.dwContentElements {
				header = ' . $configuration['elementWizardTabTitle'] . '
				show = *
			}'
		);
	}

	//Get all config files
	$path = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Denkwerk\DwContentElements\Utility\Pathes');
	$contentElements = $path->getAllDirFiles(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('dw_content_elements_source') . '/Configuration/Elements');

	//Add new content elements
	if (is_array($contentElements) && empty($contentElements) === false) {

		foreach ($contentElements as $key => $element) {

			//Load element config
			$elementConfig = \Denkwerk\DwContentElements\Service\Ini::getInstance()
				->setConfigFile('typo3conf/ext/dw_content_elements_source/Configuration/Elements/'. ucfirst($key) .'.ts')
				->loadConfig();

			if (isset($elementConfig['title'])) {

				//Add element plugin
				\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(array($elementConfig['title'], lcfirst($key)), 'CType');

				//Set element showitem
				if ((bool)$elementConfig['overWriteShowitem'] === TRUE) {
					$showItem = trim((string)$elementConfig['fields'], ',');
				} else {
					$showItem = 'CType;;4;button;1-1-1, --palette--;Headline,' . trim((string)$elementConfig['fields'], ',') . ',
            		--div--;LLL:EXT:cms/locallang_tca.xml:pages.tabs.access,starttime, endtime, fe_group';
				}
				$TCA['tt_content']['types'][lcfirst($key)]['showitem'] = $showItem;
				$TCA['tt_content']['types'][lcfirst($key)]['tx_dw_content_elements_title'] = (string)$elementConfig['title'];

				//Set rendering typoScript
				$typoScript .= '
                tt_content.' . lcfirst($key) . ' < tt_content.list.20.dwcontentelementssource_contentrenderer
                tt_content.' . lcfirst($key) . '.switchableControllerActions.Elements.1 = render';

				//Add content elements to the content elements wizard
				if ((bool)$configuration['addElementsToWizard'] === TRUE) {

					\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
                    mod.wizards.newContentElement.wizardItems.dwContentElements.elements.' . lcfirst($key) . ' {
                        icon = ' . (string)$elementConfig['icon'] . '
                        title = ' . (string)$elementConfig['title'] . '
                        description = ' . (string)$elementConfig['description'] . '
                        tt_content_defValues.CType = ' . lcfirst($key) . '
                    }
                ');
				}

			}

		}
	}

	//Add rendering typoScript
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript($_EXTKEY, 'setup', $typoScript, TRUE);
}

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