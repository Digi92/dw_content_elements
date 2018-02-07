<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

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

//Source Extension Installiert // Include plugins of the content elements
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
            $providerConfig['pluginCategory'] =
                'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xlf:mlang_tabs_tab';
        }

        // generate camelcase version of the provider
        $providerNameCamelCase = preg_replace_callback('/_([a-z])/', function ($c) {
            return strtoupper($c[1]);
        }, $provider);


        //Set own optgroup on the ctype select
        $GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'][] = array(
            0 => $providerConfig['pluginCategory'],
            1 => '--div--'
        );

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
                    //Add element plugin
                    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(
                        array(
                            $elementConfig['title'],
                            lcfirst($key)
                        ),
                        'CType',
                        $provider
                    );

                    //Set element showitem
                    if ((bool)$elementConfig['overWriteShowitem'] === true) {
                        $showItem = trim((string)$elementConfig['fields'], ',');
                    } else {
                        $showItem = 'CType;;4;button;1-1-1, colPos, --palette--;Headline,'
                            . trim((string)$elementConfig['fields'], ',') . ',
    						--div--;LLL:EXT:cms/locallang_tca.xlf:pages.tabs.access,
    					    --palette--;LLL:EXT:cms/locallang_tca.xlf:pages.palettes.visibility;hiddenonly,
    					    --palette--;LLL:EXT:cms/locallang_tca.xlf:pages.palettes.access;access';
                    }
                    $GLOBALS['TCA']['tt_content']['types'][lcfirst($key)]['showitem'] = $showItem;
                    $GLOBALS['TCA']['tt_content']['types'][lcfirst($key)]['tx_dw_content_elements_title'] =
                        (string)$elementConfig['title'];

                    //Add tab extends and if the palette "dwcAdditionalFields" exists add the fields of it
                    $GLOBALS['TCA']['tt_content']['types'][lcfirst($key)]['showitem'] .= ',
                        --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xml:pages.tabs.extended,
                        --palette--;LLL:EXT:' . $_EXTKEY .
                        '/Resources/Private/Language/locallang_db.xlf:palettes.dwcAdditionalFields;dwcAdditionalFields';

                    // Fix for the extension GridElements. GridElements needs in all elements the
                    // fields "tx_gridelements_container,tx_gridelements_columns"
                    if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('gridelements')) {
                        $GLOBALS['TCA']['tt_content']['types'][lcfirst($key)]['showitem'] .=
                            ',tx_gridelements_container,tx_gridelements_columns';
                    }

                    //Set rendering typoScript
                    $typoScript .= "\n
                    tt_content." . lcfirst($key) .
                        " < tt_content.list.20." .
                        strtolower($providerNameCamelCase) . "_" . strtolower($providerConfig['pluginName']) . " \n";

                    foreach ($providerConfig['controllerActions'] as $controller => $actions) {
                        $actionArray = explode(',', $actions);
                        foreach ($actionArray as $index => $action) {
                            $typoScript .= "tt_content." .
                                lcfirst($key) . ".switchableControllerActions." .
                                $controller . "." . ($index + 1) . " = " .
                                $action . " \n";
                        }
                    }
                }
            }
        }
    }

    //Add rendering typoScript
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
        $_EXTKEY,
        'setup',
        $typoScript,
        true
    );
}