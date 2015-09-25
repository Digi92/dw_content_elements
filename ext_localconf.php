<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem'][$_EXTKEY] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY).'Classes/Hooks/PageLayoutViewDrawItemHook.php:PageLayoutViewDrawItemHook';


\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
// unique plugin name
	'Denkwerk.' . $_EXTKEY. 'Source',
	'ContentRenderer',
	// accessible controller-action-combinations
	array('Elements' => 'render'),
	// non-cachable controller-action-combinations (they must already be enabled)
	array()
);

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
