<?php
$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('dw_content_elements');
return array(
    'Zend\Exception' => $extensionPath . 'Libraries/Zend/Exception.php',
    'Zend\Config' => $extensionPath . 'Libraries/Zend/Config.php',
    'Zend\Config\Exception' => $extensionPath . 'Libraries/Zend/Config/Exception.php',
    'Zend\Config\Ini' => $extensionPath . 'Libraries/Zend/Config/Ini.php',
);
