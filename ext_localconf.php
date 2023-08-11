<?php

if (!defined('TYPO3')) {
    die('Access denied.');
}

### - ###
// Override preview in the page view
//$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem']['dw_content_elements'] =
//    \Denkwerk\DwContentElements\Hooks\PageLayoutViewDrawItemHook::class;
### - ###

// Override preview in the list view
if (isset($GLOBALS['TCA']['tt_content']) && !isset($GLOBALS['TCA']['tt_content']['ctrl']['label_userFunc'])) {
    $GLOBALS['TCA']['tt_content']['ctrl']['label_userFunc'] =
        'Denkwerk\\DwContentElements\\UserFunc\\Tca->setTtContentTitle';
}

// Register content element
$injectorService = new \Denkwerk\DwContentElements\Service\InjectorService();
$injectorService->injectTypoScripConfiguration();
