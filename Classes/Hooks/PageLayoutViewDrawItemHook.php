<?php

namespace Denkwerk\DwContentElements\Hooks;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Sascha Zander <sascha.zander@denkwerk.com>
 *  (c) 2016 Johann Derdak <johann.derdak@denkwerk.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 *
 * @package dw_content_elements
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class PageLayoutViewDrawItemHook implements \TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface
{

    /**
     * Preprocesses the preview rendering of a content element.
     *
     * @param    \TYPO3\CMS\Backend\View\PageLayoutView $parentObject :  Calling parent object
     * @param    boolean $drawItem :      Whether to draw the item using the default functionalities
     * @param    string $headerContent : Header content
     * @param    string $itemContent :   Item content
     * @param    array $row :           Record row of tt_content
     * @return    void
     */
    public function preProcess(
        \TYPO3\CMS\Backend\View\PageLayoutView &$parentObject,
        &$drawItem,
        &$headerContent,
        &$itemContent,
        array &$row
    ) {

        //Get all config files
        $path = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Denkwerk\DwContentElements\Utility\Pathes');
        $providers = array();

        // get configurations from localconf
        $configurations = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dw_content_elements'];

        if (isset($configurations['provider']) && count($configurations['provider'])) {
            foreach ($configurations['provider'] as $extKey => $config) {
                if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded($extKey) && is_array($config)) {
                    $providers[$extKey] = $config;
                }
            }
        } elseif (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('dw_content_elements_source')) {
            $providers['dw_content_elements_source'] = array(
                'pluginName' => 'ContentRenderer',
                'controllerActions' => array('Elements' => 'render'),
                'namespace' => 'Denkwerk.DwContentElementsSource'
            );
        }

        foreach ($providers as $provider => $providerConf) {
            // build elements path
            $elementsPath = (isset($providerConfig['elementsPath']) && !empty($providerConf['elementsPath'])) ?
                $providerConfig['elementsPath'] :
                '/Configuration/Elements';

            $contentElements = $path->getAllDirFiles(
                \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($provider) . $elementsPath
            );

            if (isset($contentElements[ucfirst($row['CType'])])) {
                $drawItem = false;
                $itemContent = '<div style="font-size: 11px;">';

                //Load element config
                $elementConfig = \Denkwerk\DwContentElements\Service\Ini::getInstance()
                    ->setConfigFile($contentElements[ucfirst($row['CType'])])
                    ->loadConfig();

                //Get all preview showitem fields
                if (isset($elementConfig['previewFields'])) {
                    $fields = self::getMainFields($elementConfig['previewFields'], 'tt_content', $row);
                } else {
                    //Get all showitem fields
                    $fields = self::getMainFields($elementConfig['fields'], 'tt_content', $row);
                }

                //Set content element title
                $headerContent = '<b>' . $elementConfig['title'] . '</b><br />';

                //Set preview for the showitem fields
                $count = 0;
                foreach ($fields as $field) {
                    $itemContent .= self::renderFieldPreview(
                        $field['name'],
                        $row,
                        (isset($field['label']) &&
                        empty($field['label']) === false ?
                            $field['label'] :
                            $parentObject->itemLabels[$field['name']]
                        )
                    );

                    $count++;
                    if ($count >= 10) {
                        $itemContent .= '<p><b>...</b></p>';
                        break;
                    }
                }
                $itemContent .= '</div>';
            }
        }
    }

    /**
     * Render a preview for the giving field
     *
     * @param $fieldName
     * @param $row
     * @param string $itemLabels
     * @param string $fieldTable
     * @return string
     */
    public function renderFieldPreview($fieldName, $row, $itemLabels = '', $fieldTable = 'tt_content')
    {
        $filedContent = '';
        $fieldValue = $row[$fieldName];

        if (isset($fieldName) && isset($fieldValue)) {
            $filedContent .= '<div>';
            $fieldConfig = \TYPO3\CMS\Backend\Utility\BackendUtility::getTcaFieldConfiguration($fieldTable, $fieldName);

            switch ($fieldConfig['type']) {
                case "input":
                    //If field is has a link wizard
                    if (isset($fieldConfig['wizards']['link']) && empty($fieldValue) === false) {
                        /*** @var \Denkwerk\DwContentElements\Service\Url $urlService */
                        $urlService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                            'Denkwerk\\DwContentElements\\Service\\Url'
                        );
                        $fieldValue = $urlService->getUrl($row['pid'], $fieldValue);
                    }

                    $filedContent .= '<p style="padding-right: 5px;margin:0;">
                            <b>' . $itemLabels . '</b><br />' .
                            strip_tags((string)$fieldValue) .
                        '</p>';
                    break;
                case "text":
                    $filedContent .= '<p style="padding-right: 5px;margin:0;">
                            <b>' . $itemLabels . '</b><br />' .
                            substr(strip_tags((string)$fieldValue), 0, 100) .
                            (strlen((string)$fieldValue) > 100 ? '...' : '') .
                        '</p>';
                    break;
                case "check":
                    $filedContent .= '<p style="padding-right: 5px;margin:0;">
                            <b>' . $itemLabels . '</b><br />' .
                            ((bool)$fieldValue ? '&#10004;' : '&#10008;') .
                        '</p>';
                    break;
                case "radio":
                    $filedContent .= '<p style="padding-right: 5px;margin:0;">
                            <b>' . $itemLabels . '</b><br />' .
                            (string)$fieldValue .
                        '</p>';
                    break;
                case "select":
                    //TODO: Wrong preview if the select use mm!!!
                    $filedContent .= '<p style="padding-right: 5px;margin:0;"><b>' . $itemLabels . '</b><br />';
                    $items = array();

                    //Get all items
                    $formEngine = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                        'TYPO3\\CMS\\Backend\\Form\\FormEngine'
                    );
                    $items = $formEngine->getSelectItems(
                        'tt_content',
                        $fieldName,
                        $row,
                        array(
                            'fieldConf' => $GLOBALS['TCA'][$fieldTable]['columns'][$fieldName],
                            'fieldTSConfig' => null
                        )
                    );

                    switch ($fieldConfig['renderMode']) {
                        case "checkbox":
                            $value = explode(',', $fieldValue);
                            foreach ($items as $item) {
                                $filedContent .= $item[0] . '<br />' .
                                    (in_array($item[1], $value) ? '&#10004;' : '&#10008;');
                            }
                            break;
                        case "singlebox":
                            $value = explode(',', $fieldValue);
                            foreach ($items as $item) {
                                $filedContent .= $item[0] . '<br />' .
                                    (in_array($item[1], $value) ? '&#10004;' : '&#10008;');
                            }
                            break;
                        default:
                            foreach ($items as $item) {
                                if ($item[1] == $fieldValue) {
                                    $filedContent .= $item[0] . ' &#10004;';
                                }
                            }
                    }
                    $filedContent .= '</p>';

                    break;
                case "group":
                    $filedContent .= '<p style="padding-right: 5px;margin:0;"><b>' .
                        $itemLabels . '</b><br />' . (string)$fieldValue . '</p>';
                    break;
                case "inline":
                    $filedContent .= '<p style="padding-right: 5px;margin:0;"><b>' .
                        $itemLabels . '</b>';

                    if (isset($fieldConfig['foreign_table']) &&
                        $fieldConfig['foreign_table'] === 'sys_file_reference'
                    ) {
                        //TODO: Need to add function to set the file title
                        $filePreview = \TYPO3\CMS\Backend\Utility\BackendUtility::thumbCode(
                            $row,
                            $fieldTable,
                            $fieldName,
                            $GLOBALS['BACK_PATH'],
                            '',
                            null,
                            0,
                            '',
                            '150x150',
                            true
                        );
                        $filedContent .= '<br />';
                        $filedContent .= ($filePreview != '' ? $filePreview : 'No File');
                    } else {
                        if (isset($fieldConfig['foreign_table'])) {
                            $contentObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                                'TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer'
                            );
                            $contentObj->data = $row;
                            $count = 0;
                            $filedContent .= '<p style="padding-right: 5px;margin:0;">';

                            foreach (\Denkwerk\DwContentElements\Service\IrreService::getRelations(
                                $contentObj,
                                $fieldConfig['foreign_table']
                            ) as $item) {
                                $irreItemLabel = $item[$GLOBALS['TCA'][$fieldConfig['foreign_table']]['ctrl']['label']];
                                $filedContent .= ($irreItemLabel != '' ? substr((string)$irreItemLabel, 0, 25) .
                                    (strlen((string)$irreItemLabel) > 25 ? '...' : '') :
                                    \TYPO3\CMS\Backend\Utility\BackendUtility::getNoRecordTitle(true)
                                );
                                $filedContent .= '</br>';

                                $count++;
                                if ($count >= 5) {
                                    $filedContent .= '...';
                                    break;
                                }
                            }
                            $filedContent .= '</p>';
                        }
                    }
                    $filedContent .= '</p>';

                    break;
                default:
                    $filedContent .= '<p style="padding-right: 5px;margin:0;"><b/>' .
                        $itemLabels . '</b><br />' . $fieldValue . '</p>';
            }
            $filedContent .= '</div>';
        }
        return $filedContent;
    }

    /**
     * Created an array list with all fields from the showitem string
     * Function base from TYPO3\CMS\Backend\Form\FormEngine function "getMainFields" line:864
     *
     * @param $itemList
     * @param $table
     * @param array $row
     * @return array
     */
    public function getMainFields($itemList, $table, array $row)
    {
        $result = array();

        $fieldsArray = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(
            ',',
            $itemList,
            true
        );

        // Set fields and replace palettes and divs
        foreach ($fieldsArray as $fieldInfo) {
            // Exploding subparts of the field configuration:
            $field = self::explodeSingleFieldShowItemConfiguration($fieldInfo);

            if (!in_array($field['fieldName'], $excludeElements = array())) {
                // If field exist add this to result
                if ($GLOBALS['TCA'][$table]['columns'][$field['fieldName']]) {
                    //Translate the label
                    if ($field['fieldLabel'] !== null &&
                        strpos($field['fieldLabel'], "LLL:EXT:") !== false
                    ) {
                        $field['fieldLabel'] = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                            $field['fieldLabel'],
                            ''
                        );
                    }

                    array_push($result, array('name' => $field['fieldName'], 'label' => $field['fieldLabel']));
                } elseif ($field['fieldName'] == '--div--') {
                    // Do nothing!
                } elseif ($field['fieldName'] == '--palette--') {
                    if ($field['paletteName']) {
                        // Load the palette TCEform elements
                        if ($GLOBALS['TCA'][$table] &&
                            is_array($GLOBALS['TCA'][$table]['palettes'][$field['paletteName']])
                        ) {
                            $palettesItemList = $GLOBALS['TCA'][$table]['palettes'][$field['paletteName']]['showitem'];
                            if ($palettesItemList) {
                            // Call the palette showitem with the function "getMainFields" and add the result to $result
                                foreach (self::getMainFields($palettesItemList, 'tt_content', $row) as $field) {
                                    array_push($result, $field);
                                }
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }


    /**
     * A single field of TCA 'types' 'showitem' can have four semicolon separated configuration options:
     *   fieldName: Name of the field to be found in TCA 'columns' section
     *   fieldLabel: An alternative field label
     *   paletteName: Name of a palette to be found in TCA 'palettes' section that is rendered after this field
     *   extra: Special configuration options of this field
     *
     * @param string $field Semicolon separated field configuration
     * @throws \RuntimeException
     * @return array
     */
    protected function explodeSingleFieldShowItemConfiguration($field)
    {
        $fieldArray = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(';', $field);
        if (empty($fieldArray[0])) {
            throw new \RuntimeException('Field must not be empty', 1426448465);
        }
        return array(
            'fieldName' => $fieldArray[0],
            'fieldLabel' => $fieldArray[1] ?: null,
            'paletteName' => $fieldArray[2] ?: null,
        );
    }
}
