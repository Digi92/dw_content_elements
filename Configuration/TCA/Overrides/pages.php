<?php

if (!defined('TYPO3')) {
    die('Access denied.');
}

//================================================ TypoScript Begin ==================================================//

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'dw_content_elements',
    'Configuration/TypoScript',
    'Content Element configuration'
);

//================================================= TypoScript End ===================================================//
