<?php

use Denkwerk\DwContentElements\Hooks\ExtTablesInclusionPostProcessingHook;

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem'][$_EXTKEY] =
    'Denkwerk\\DwContentElements\\Hooks\\PageLayoutViewDrawItemHook';

/**
 * Note: This hook will load all content element configuration and add the plugin configuration after
 * all TYPO3 TCA loading tasks.
 */
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['GLOBAL']['extTablesInclusion-PostProcessing'][$_EXTKEY] =
    ExtTablesInclusionPostProcessingHook::class;

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

/** ToDo: Needed in TYPO3 8.7? */
$GLOBALS['TCA']['tt_content']['columns']['colPos']['config']['itemsProcFunc'] =
    'Denkwerk\DwContentElements\Backend\ItemsProcFuncs\ColPosList->itemsProcFunc';

// Override preview in the list view
if (!isset($GLOBALS['TCA']['tt_content']['ctrl']['label_userFunc'])) {
    $GLOBALS['TCA']['tt_content']['ctrl']['label_userFunc'] =
        'Denkwerk\\DwContentElements\\UserFunc\\Tca->setTtContentTitle';
}

