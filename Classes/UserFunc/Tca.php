<?php

namespace Denkwerk\DwContentElements\UserFunc;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Sylt Steen <sylt-luwe.steen@denkwerk.com>
 *  (c) 2018 Sascha Zander <sascha.zander@denkwerk.com>
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

use Denkwerk\DwContentElements\Service\IniProviderService;
use Denkwerk\DwContentElements\Service\IniService;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Tca
 * @package Denkwerk\DwContentElements\UserFunc
 *
 * Note: Override preview in the list view
 */
class Tca
{
    /**
     * @var IniService $iniService
     */
    protected $iniService = null;

    /**
     * @var IniProviderService $iniProviderService
     */
    protected $iniProviderService = null;

    /**
     * InjectorService constructor.
     */
    function __construct()
    {
        $this->iniService = GeneralUtility::makeInstance(IniService::class);
        $this->iniProviderService = GeneralUtility::makeInstance(IniProviderService::class);
    }

    /**
     * Preprocessed the preview rendering of a content element in the list view.
     *
     * @param $params
     * @return mixed
     */
    public function setTtContentTitle(&$params)
    {
        // Set the title by using the header field like the TYPO3 default settings
        $params['title'] = $params['row']['header'];

        // Load all provider configurations as array
        $providers = $this->iniProviderService->loadProvider();

        // Load all content elements config files
        $elementsConfigFilesArray = [];
        if (count($providers) > 0) {
            foreach ($providers as $provider => $providerConfig) {
                $providerElementsConfigFiles = $this->iniService->loadAllContentElementsConfigFiles(
                    $provider,
                    $providerConfig
                );
                $elementsConfigFilesArray = array_merge($elementsConfigFilesArray, $providerElementsConfigFiles);
            }
        }

        // If it is an dwc content element
        if (isset($params['row']['CType']) &&
            !is_array($params['row']['CType']) &&
            is_array($elementsConfigFilesArray) &&
            isset($elementsConfigFilesArray[ucfirst($params['row']['CType'])])
        ) {
            $title = '';

            //Load content element config
            $elementConfig = $this->iniService->loadConfig(
                $elementsConfigFilesArray[ucfirst($params['row']['CType'])]
            );

            // If the content element has the config "previewListFields" set the value of this fields
            if (isset($elementConfig['previewListFields']) &&
                empty($elementConfig['previewListFields']) == false &&
                is_array($previewListFields = array_map(
                    'trim',
                    explode(',', $elementConfig['previewListFields'])
                )) &&
                is_numeric($params['row']['uid'])
            ) {
                /** @var QueryBuilder $queryBuilder */
                $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                    ->getQueryBuilderForTable('tt_content');
                $result = $queryBuilder
                    ->select(...$previewListFields)
                    ->from('tt_content')
                    ->where('uid=' . $params['row']['uid'])
                    ->setMaxResults(1)
                    ->execute();
                $row = $result->fetch();

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
