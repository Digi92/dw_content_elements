<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem'][$_EXTKEY] =
    'Denkwerk\\DwContentElements\\Hooks\\PageLayoutViewDrawItemHook';

$configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['dw_content_elements']);
$configurations = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dw_content_elements'];
$providers = array();

if (isset($configurations['provider']) && count($configurations['provider'])) {
    foreach ($configurations['provider'] as $extKey => $config) {
        if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded($extKey) && is_array($config)) {
            $providers[$extKey] = $config;
        }
    }
} elseif (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('dw_content_elements_source')) {
    $providers['dw_content_elements_source'] = array(
        'pluginName' => 'ContentRenderer',
        'controllerActions' => array('Elements' => 'render'),
        'namespace' => 'Denkwerk.' . $_EXTKEY. 'Source'
    );
}

if (count($providers) > 0) {
    foreach ($providers as $provider => $providerConfig) {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        // unique plugin name
            $providerConfig['namespace'],
            $providerConfig['pluginName'],
            // accessible controller-action-combinations
            $providerConfig['controllerActions'],
            // non-cachable controller-action-combinations (they must already be enabled)
            array()
        );
    }
}

/**
 * Only a hotfix for the bug: Missing rendering configuration for the content elements
 * Die Rendering Definition sollte unter $GLOBALS['TSFE']->tmpl->setup['tt_content.'] stehen.
 * Es kann unter nicht geklärten bedingungen vorkommen das diese Konfiguration nicht im Cache ist oder geladen wird
 *
 * @ToDo: Remove Hotfix or refactor
 */
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\\CMS\\Frontend\\ContentObject\\CaseContentObject'] = array(
    'className' => 'Denkwerk\\DwContentElements\\Xclass\\CaseContentObject',
);


$GLOBALS['TCA']['tt_content']['columns']['colPos']['config']['itemsProcFunc'] =
    'Denkwerk\DwContentElements\Backend\ItemsProcFuncs\ColPosList->itemsProcFunc';

// Previe in the list view
if (!isset($GLOBALS['TCA']['tt_content']['ctrl']['label_userFunc'])) {
    $GLOBALS['TCA']['tt_content']['ctrl']['label_userFunc'] =
        'Denkwerk\\DwContentElements\\UserFunc\\Tca->setTtContentTitle';
}


// Extension manager configuration: used as default configuration
$configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['dw_content_elements']);

// get configurations from localconf
$configurations = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dw_content_elements'];

// initialize provieders array
$providers = array();

if (isset($configurations['provider']) && count($configurations['provider'])) {
    foreach ($configurations['provider'] as $extKey => $config) {
        if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded($extKey) && is_array($config)) {
            $providers[$extKey] = $config;
        }
    }
} elseif (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('dw_content_elements_source')) {
    $providers['dw_content_elements_source'] = array(
        'pluginName' => 'ContentRenderer',
        'controllerActions' => array('Elements' => 'render')
    );
}

//Source Extension Installiert // Include addElementsToWizard
if (count($providers) > 0) {
    foreach ($providers as $provider => $providerConfig) {
        // Set default values for provider configuration
        if (!isset($providerConfig['addElementsToWizard'])) {
            $providerConfig['addElementsToWizard'] = $configuration['addElementsToWizard'];
        }

        if (!isset($providerConfig['elementWizardTabTitle']) || empty($providerConfig['elementWizardTabTitle'])) {
            $providerConfig['elementWizardTabTitle'] = $configuration['elementWizardTabTitle'];
        }

        // generate camelcase version of the provider
        $providerNameCamelCase = preg_replace_callback('/_([a-z])/', function ($c) {
            return strtoupper($c[1]);
        }, $provider);

        //Add content element wizard tab
        if ((bool)$providerConfig['addElementsToWizard'] === true &&
            (bool)$configuration['addElementsToWizard'] === true
        ) {
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
                'mod.wizards.newContentElement.wizardItems.' . $providerNameCamelCase . ' {
    				header = ' . $providerConfig['elementWizardTabTitle'] . '
    				show = *
    			}'
            );
        }

        // build elements path
        $elementsPath = (isset($providerConfig['elementsPath']) && !empty($providerConfig['elementsPath'])) ?
            $providerConfig['elementsPath'] :
            '/Configuration/Elements';

        //Get all config files
        $path = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Denkwerk\DwContentElements\Utility\Pathes');
        $contentElements = $path->getAllDirFiles(
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($provider) . $elementsPath
        );

        //Add new content elements
        if (is_array($contentElements) && empty($contentElements) === false) {
            foreach ($contentElements as $key => $element) {
                //Load element config
                $elementConfig = \Denkwerk\DwContentElements\Service\Ini::getInstance()
                    ->setConfigFile($element)
                    ->loadConfig();

                if (isset($elementConfig['title'])) {
                    //Add content elements to the content elements wizard
                    if ((bool)$providerConfig['addElementsToWizard'] === true &&
                        (bool)$configuration['addElementsToWizard'] === true
                    ) {
                        if (version_compare(TYPO3_branch, '7.6', '<')) {
                            // Fallback for TYPO3 6.2

                            // Fallback icon
                            $iconPath = '../../typo3conf/ext/' . $_EXTKEY . '/ext_icon.png';

                            // Set custom icon
                            if ($elementConfig['icon']) {
                                $iconPath =  (string)$elementConfig['icon'];
                            }

                            // Add the icon to the content element config
                            $icon = 'icon = ' . $iconPath;
                        } else {
                            // Fallback icon
                            $iconIdentifier = 'content-textpic';

                            // Registration the content element icon, if set
                            if ($elementConfig['icon']) {
                                /** @var \TYPO3\CMS\Core\Imaging\IconRegistry $iconRegistry */
                                $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                                    'TYPO3\\CMS\\Core\\Imaging\\IconRegistry'
                                );
                                $iconIdentifier = 'dwc-' . lcfirst($key);
                                $iconRegistry->registerIcon(
                                    $iconIdentifier,
                                    \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
                                    array(
                                        'source' => (string)$elementConfig['icon']
                                    )
                                );
                            }

                            // Add the icon to the content element config
                            $icon = 'iconIdentifier = ' . $iconIdentifier;
                        }

                        // Set conten element wizardItems
                        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
        					mod.wizards.newContentElement.wizardItems.' .
                            $providerNameCamelCase . '.elements.' . lcfirst($key) . ' {
                                ' . $icon . '
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
}