<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Content element configuration');

require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('dw_content_elements_source') . '/setup_content_elements.php');

$typoScript = '[GLOBAL] ';

// Extension manager configuration
$configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['dw_content_elements']);

//Add content element wizard tab
if((bool)$configuration['addElementsToWizard'] === true) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
        mod.wizards.newContentElement.wizardItems.dwContentElements {
            header = ' . $configuration['elementWizardTabTitle'] . '
            show = *
        }'
    );
}

//Add new content elements
if(is_array($contentElements)) {

    foreach ($contentElements as $key => $element) {

        if (isset($element['title'])) {

            //Add element plugin
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(array($element['title'], $key), 'CType');

            //Set element showitem
            if ((bool)$element['overWriteShowitem'] === TRUE) {
                $showItem = trim((string)$element['fields'], ',');
            } else {
                $showItem = 'CType;;4;button;1-1-1, --palette--;Headline,' . trim((string)$element['fields'], ',') . ',
            --div--;LLL:EXT:cms/locallang_tca.xml:pages.tabs.access,starttime, endtime, fe_group';
            }
            $TCA['tt_content']['types'][$key]['showitem'] = $showItem;
            $TCA['tt_content']['types'][$key]['tx_dw_content_elements_title'] = $element['title'];

            //Set rendering typoScript
            $typoScript .= '
                tt_content.' . $key . ' < tt_content.list.20.dwcontentelementssource_contentrenderer
                tt_content.' . $key . '.switchableControllerActions.Elements.1 = render';

            //Add content elements to the content elements wizard
            if((bool)$configuration['addElementsToWizard'] === true) {

                \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
                    mod.wizards.newContentElement.wizardItems.dwContentElements.elements.' . $key . ' {
                        icon = ' . $element['icon'] . '
                        title = ' . $element['title'] . '
                        description = ' . $element['description'] . '
                        tt_content_defValues.CType = ' . $key . '
                    }
                ');
            }

        }

    }
}

//Add rendering typoScript
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript($_EXTKEY, 'setup', $typoScript, true);