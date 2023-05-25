<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

if (!defined('TYPO3')) {
    die('Access denied.');
}

ExtensionManagementUtility::addStaticFile(
    'dw_content_elements_source',
    'Configuration/TypoScript',
    'Content element configuration'
);


//============================================== Einbindung Irrel BEGIN ==============================================//

//--------------------------------- tx_dwcontentelementssource_domain_model_listitem ---------------------------------//
ExtensionManagementUtility::allowTableOnStandardPages(
    'tx_dwcontentelementssource_domain_model_listitem'
);

//=============================================== Einbindung Irrel END ===============================================//
