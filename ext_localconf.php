<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

//$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem'][$_EXTKEY] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY).'Classes/Hooks/PageLayoutViewDrawItemHook.php:PageLayoutViewDrawItemHook';


\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
// unique plugin name
	'Denkwerk.' . $_EXTKEY. 'Source',
	'ContentRenderer',
	// accessible controller-action-combinations
	array('Elements' => 'render'),
	// non-cachable controller-action-combinations (they must already be enabled)
	array()
);
