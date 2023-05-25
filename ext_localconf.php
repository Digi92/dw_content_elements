<?php

if (!defined('TYPO3')) {
    die('Access denied.');
}

/**
 * Only a hotfix for the bug: Missing rendering configuration for the content elements
 * Die Rendering Definition sollte unter $GLOBALS['TSFE']->tmpl->setup['tt_content.'] stehen.
 * Es kann unter nicht geklärten bedingungen vorkommen das diese Konfiguration nicht im Cache ist oder geladen wird
 *
 * @ToDo: Remove Hotfix or refactor
 */
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Frontend\ContentObject\CaseContentObject::class] = array(
    'className' => \Denkwerk\DwContentElements\Xclass\CaseContentObject::class,
);

// Override preview in the page view
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem']['dw_content_elements'] =
    \Denkwerk\DwContentElements\Hooks\PageLayoutViewDrawItemHook::class;

// Override preview in the list view
if (isset($GLOBALS['TCA']['tt_content']) && !isset($GLOBALS['TCA']['tt_content']['ctrl']['label_userFunc'])) {
    $GLOBALS['TCA']['tt_content']['ctrl']['label_userFunc'] =
        'Denkwerk\\DwContentElements\\UserFunc\\Tca->setTtContentTitle';
}

// Register content element plugins
$injectorService = new \Denkwerk\DwContentElements\Service\InjectorService();
$injectorService->injectPluginConfiguration();
