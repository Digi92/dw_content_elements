<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Content element configuration');

// Extension manager configuration: used as default configuration
$configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['dw_content_elements']);

// get configurations from localconf
$configurations = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dw_content_elements'];

// initialize provieders array
$providers = array();

if (isset($configurations['provider']) && count($configurations['provider'])) {
    foreach ($configurations['provider'] as $extKey => $config) {
        if(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded($extKey) && is_array($config)) {
            $providers[$extKey] = $config;
        }
    }
} else if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('dw_content_elements_source')) {
    $providers['dw_content_elements_source'] = array(
        'pluginName' => 'ContentRenderer',
        'controllerActions' => array('Elements' => 'render')
    );
}

//Source Extension Installiert
if (count($providers) > 0) {

	$typoScript = '[GLOBAL] ';

    foreach ($providers as $provider => $providerConfig) {

        // Set default values for provider configuration
        if (!isset($providerConfig['addElementsToWizard'])) {
            $providerConfig['addElementsToWizard'] = $configuration['addElementsToWizard'];
        }

        if (!isset($providerConfig['elementWizardTabTitle']) || empty($providerConfig['elementWizardTabTitle'])) {
            $providerConfig['elementWizardTabTitle'] = $configuration['elementWizardTabTitle'];
        }

        if (!isset($providerConfig['pluginCategory']) || empty($providerConfig['pluginCategory'])) {
            $providerConfig['pluginCategory'] = 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xlf:mlang_tabs_tab';
        }

        // generate camelcase version of the provider
        $providerNameCamelCase = preg_replace_callback('/_([a-z])/', function($c) {
            return strtoupper($c[1]);
        }, $provider);

    	//Add content element wizard tab
    	if ((bool)$providerConfig['addElementsToWizard'] === TRUE && (bool)$configuration['addElementsToWizard'] === TRUE) {
    		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
    			mod.wizards.newContentElement.wizardItems.' . $providerNameCamelCase . ' {
    				header = ' . $providerConfig['elementWizardTabTitle'] . '
    				show = *
    			}'
    		);
    	}

    	//Set own optgroup on the ctype select
    	$GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'][] = array(
    		0 => 'LLL:EXT:' . $providerConfig['pluginCategory'],
    		1 => '--div--'
    	);

        // build elements path
        $elementsPath = (isset($providerConfig['elementsPath']) && !empty($providerConfig['elementsPath'])) ?
            $providerConfig['elementsPath'] :
            '/Configuration/Elements';

    	//Get all config files
    	$path = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Denkwerk\DwContentElements\Utility\Pathes');
    	$contentElements = $path->getAllDirFiles(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($provider) . $elementsPath);

    	//Add new content elements
    	if (is_array($contentElements) && empty($contentElements) === false) {

    		foreach ($contentElements as $key => $element) {

    			//Load element config
    			$elementConfig = \Denkwerk\DwContentElements\Service\Ini::getInstance()
    				->setConfigFile($element)
    				->loadConfig();

    			if (isset($elementConfig['title'])) {

    				//Add element plugin
    				\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(array($elementConfig['title'], lcfirst($key)), 'CType', $provider);

    				//Set element showitem
    				if ((bool)$elementConfig['overWriteShowitem'] === TRUE) {
    					$showItem = trim((string)$elementConfig['fields'], ',');
    				} else {
    					$showItem = 'CType;;4;button;1-1-1, --palette--;Headline,' . trim((string)$elementConfig['fields'], ',') . ',
    						--div--;LLL:EXT:cms/locallang_tca.xlf:pages.tabs.access,
    					    --palette--;LLL:EXT:cms/locallang_tca.xlf:pages.palettes.visibility;hiddenonly,
    					    --palette--;LLL:EXT:cms/locallang_tca.xlf:pages.palettes.access;access';
    				}
    				$TCA['tt_content']['types'][lcfirst($key)]['showitem'] = $showItem;
    				$TCA['tt_content']['types'][lcfirst($key)]['tx_dw_content_elements_title'] = (string)$elementConfig['title'];

                    //Add tab extends and if the palette "dwcAdditionalFields" exists add the fields of it
                    $TCA['tt_content']['types'][lcfirst($key)]['showitem'] .= ',
                        --div--;LLL:EXT:cms/locallang_tca.xml:pages.tabs.extended,
                        --palette--;LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xlf:palettes.dwcAdditionalFields;dwcAdditionalFields';

    				//Fix for the extension GridElements. GridElements needs in all elements the fields "tx_gridelements_container,tx_gridelements_columns"
    				if(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('gridelements')) {
    					$TCA['tt_content']['types'][lcfirst($key)]['showitem'] .= ',tx_gridelements_container,tx_gridelements_columns';
    				}

    				//Set rendering typoScript
    				$typoScript .= "\n
    				tt_content." . lcfirst($key) . " < tt_content.list.20." . strtolower($providerNameCamelCase) . "_" . strtolower($providerConfig['pluginName']) . " \n";

                    foreach ($providerConfig['controllerActions'] as $controller => $actions) {
                        $actionArray = explode(',', $actions);
                        foreach ($actionArray as $index => $action) {
        				    $typoScript .= "tt_content." . lcfirst($key) . ".switchableControllerActions." . $controller . "." . $index . " = " . $cation . " \n";
                        }
                    }

    				//Add content elements to the content elements wizard
    				if ((bool)$providerConfig['addElementsToWizard'] === TRUE && (bool)$configuration['addElementsToWizard'] === TRUE) {
    					\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
        					mod.wizards.newContentElement.wizardItems.' . $providerNameCamelCase . '.elements.' . lcfirst($key) . ' {
        						icon = ' . ($elementConfig['icon'] ? (string)$elementConfig['icon'] : '../../typo3conf/ext/' . $_EXTKEY . '/ext_icon.png') . '
        						title = ' . (string)$elementConfig['title'] . '
        						description = ' . (string)$elementConfig['description'] . '
        					    tt_content_defValues.CType = ' . lcfirst($key) . '
        					}
        				');
    				}

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