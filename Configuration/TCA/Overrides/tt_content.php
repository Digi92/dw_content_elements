<?php

use Denkwerk\DwContentElements\Service\InjectorService;

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// Preview in the list view
if (isset($GLOBALS['TCA']['tt_content']) && !isset($GLOBALS['TCA']['tt_content']['ctrl']['label_userFunc'])) {
    $GLOBALS['TCA']['tt_content']['ctrl']['label_userFunc'] =
        'Denkwerk\DwContentElements\UserFunc\Tca->setTtContentTitle';
}

// Register content element plugins
$injectorService = new InjectorService();
$injectorService->injectTca();
