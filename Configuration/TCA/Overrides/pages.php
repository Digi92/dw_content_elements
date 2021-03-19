<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

//================================================ TypoScript Begin ==================================================//

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'dw_content_elements',
    'Configuration/TypoScript',
    'Content Element configuration'
);

//================================================= TypoScript End ===================================================//
