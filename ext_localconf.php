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
