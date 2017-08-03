<?php
namespace Denkwerk\DwContentElements\UserFunc;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Sylt Steen <sylt-luwe.steen@denkwerk.com>
 *  (c) 2016 Sascha Zander <sascha.zander@denkwerk.com>
 *
 *  All rights reserved
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Class with functions for TCA
 *
 * @package dw_content_elements
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tca
{

    /**
     * Preprocesses the preview rendering of a content element in the list view.
     *
     * @param $params
     * @return mixed
     */
    public function setTtContentTitle(&$params)
    {
        // Set the title by using the header field like the TYPO3 default settings
        $params['title'] = $params['row']['header'];

        //Get all config files
        $path = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Denkwerk\DwContentElements\Utility\Pathes');
        $contentElements = $path->getAllDirFiles(
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('dw_content_elements_source') .
            '/Configuration/Elements'
        );

        // If it is an dwc content element
        if (isset($params['row']['CType']) &&
            !is_array($params['row']['CType']) &&
            isset($contentElements[ucfirst($params['row']['CType'])])
        ) {
            $title = '';

            //Load element config
            $elementConfig = \Denkwerk\DwContentElements\Service\Ini::getInstance()
                ->setConfigFile($contentElements[ucfirst($params['row']['CType'])])
                ->loadConfig();

            // If the content element has the config "previewListFields" set the value of this fields
            if (isset($elementConfig['previewListFields']) &&
                empty($elementConfig['previewListFields']) == false &&
                is_numeric($params['row']['uid'])
            ) {
                //Get all preview field values
                $row = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
                    $elementConfig['previewListFields'],
                    'tt_content',
                    'uid=' . $params['row']['uid']
                );

                if (empty($row) === false) {
                    // Removed all empty entries and make a comma separated string
                    $title = implode(', ', array_filter($row));
                }
            }

            // If no title set use the content element name
            if (empty($title)) {
                $title = (isset($elementConfig['title']) ? $elementConfig['title'] : $params['CType']);
            }

            // Set the result title
            $params['title'] = $title;
        }

        return $params;
    }
}
